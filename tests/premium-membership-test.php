#!/usr/bin/env php
<?php
/**
 * Manual Integration Test for Premium Membership Feature
 * Run: php tests/premium-membership-test.php
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

// Boot the application
$app->boot();

use App\Models\PremiumMembership;
use App\Models\User;
use App\Models\MemberWallet;
use App\Models\MemberBalanceLedger;
use App\Models\Order;
use App\Events\OrderPaid;
use Illuminate\Support\Facades\Event;

class PremiumMembershipTestSuite
{
    protected $passCount = 0;
    protected $failCount = 0;
    protected $testUser = null;

    public function run()
    {
        echo "\n==============================================================\n";
        echo "   Premium Membership Feature - Manual Integration Tests\n";
        echo "==============================================================\n\n";

        try {
            // Test 1: Model Creation
            $this->test_model_creation();
            
            // Test 2: User Premium Status
            $this->test_user_premium_status();
            
            // Test 3: Purchase Flow
            $this->test_purchase_flow();
            
            // Test 4: Membership Status Checks
            $this->test_membership_status_checks();
            
            // Test 5: Cashback Event
            $this->test_cashback_event();
            
            // Test 6: Multiple Orders
            $this->test_multiple_orders();
            
            $this->summary();
        } catch (\Exception $e) {
            echo "❌ Fatal Error: " . $e->getMessage() . "\n";
            echo "   " . $e->getFile() . ":" . $e->getLine() . "\n";
            die(1);
        }
    }

    protected function test_model_creation()
    {
        echo "[TEST] 1. Model Creation & Relations\n";
        
        try {
            // Create a test user
            $this->testUser = User::factory()->create();
            echo "   - User created: {$this->testUser->id}\n";
            
            $this->assertNotNull($this->testUser->id, "User created");
            
            // Create a premium membership
            $membership = PremiumMembership::factory()->create([
                'user_id' => $this->testUser->id,
                'status' => 'pending',
                'price' => 100000,
            ]);
            
            echo "   - Membership created: {$membership->id}\n";
            
            $this->assertNotNull($membership->id, "Membership created");
            $this->assertEqual($membership->user_id, $this->testUser->id, "Membership belongs to user");
            $this->assertEqual($membership->status, 'pending', "Membership status is pending");
            $this->assertEqual($membership->price, 100000, "Membership price is 100000");
            
            echo "   ✓ Model creation and relations working\n\n";
        } catch (\Exception $e) {
            echo "   ✗ Exception: " . $e->getMessage() . "\n";
            echo "   " . $e->getFile() . ":" . $e->getLine() . "\n\n";
            $this->fail("Model creation failed: " . $e->getMessage());
        }
    }

    protected function test_user_premium_status()
    {
        echo "[TEST] 2. User Premium Status Checking\n";
        
        try {
            // Create new user for this test
            $testUser = User::factory()->create();
            
            // User should not be premium initially
            $this->assertFalse($testUser->isPremium(), "User not premium initially");
            
            // Activate premium
            $testUser->update(['premium_expires_at' => now()->addYear()]);
            $testUser->refresh();
            
            $this->assertTrue($testUser->isPremium(), "User is premium after activation");
            
            // Test expired premium
            $testUser->update(['premium_expires_at' => now()->subDay()]);
            $testUser->refresh();
            
            $this->assertFalse($testUser->isPremium(), "User not premium when expired");
            
            echo "   ✓ User premium status checks working\n\n";
        } catch (\Exception $e) {
            $this->fail("Premium status check failed: " . $e->getMessage());
        }
    }

    protected function test_purchase_flow()
    {
        echo "[TEST] 3. Purchase Flow\n";
        
        try {
            // Create new user for this test
            $testUser = User::factory()->create();
            
            // Step 1: Create pending membership
            $membership = $testUser->premiumMemberships()->create([
                'price' => 100000,
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
            ]);
            
            $this->assertNotNull($membership->id, "Pending membership created");
            $this->assertEqual($membership->status, 'pending', "Status is pending");
            $this->assertNull($membership->payment_proof_path, "No proof uploaded yet");
            
            // Step 2: Upload proof (simulate)
            $membership->update(['payment_proof_path' => 'premium-proof/test.jpg']);
            $this->assertEqual($membership->payment_proof_path, 'premium-proof/test.jpg', "Proof uploaded");
            
            // Step 3: Admin activates membership
            $membership->update([
                'status' => 'active',
                'started_at' => now(),
                'expires_at' => now()->addYear(),
            ]);
            $testUser->update(['premium_expires_at' => now()->addYear()]);
            
            $this->assertEqual($membership->status, 'active', "Status is active");
            $this->assertTrue($membership->isActive(), "Membership is active");
            $this->assertFalse($membership->isExpired(), "Membership not expired");
            
            echo "   ✓ Purchase flow working\n\n";
        } catch (\Exception $e) {
            $this->fail("Purchase flow failed: " . $e->getMessage());
        }
    }

    protected function test_membership_status_checks()
    {
        echo "[TEST] 4. Membership Status Checks\n";
        
        try {
            // Create new user for this test
            $testUser = User::factory()->create();
            
            // Create active membership
            $activeMembership = PremiumMembership::factory()->create([
                'user_id' => $testUser->id,
                'status' => 'active',
                'started_at' => now()->subDay(),
                'expires_at' => now()->addYear(),
            ]);
            
            $this->assertTrue($activeMembership->isActive(), "Active membership detected");
            $this->assertFalse($activeMembership->isExpired(), "Active not expired");
            
            $daysRemaining = $activeMembership->daysRemaining();
            $this->assertTrue($daysRemaining > 200, "Days remaining calculated correctly");
            
            // Create expired membership
            $expiredMembership = PremiumMembership::factory()->create([
                'user_id' => $testUser->id,
                'status' => 'active',
                'started_at' => now()->subYear(),
                'expires_at' => now()->subDay(),
            ]);
            
            $this->assertFalse($expiredMembership->isActive(), "Expired membership not active");
            $this->assertTrue($expiredMembership->isExpired(), "Expired membership detected");
            
            echo "   ✓ Membership status checks working\n\n";
        } catch (\Exception $e) {
            $this->fail("Status checks failed: " . $e->getMessage());
        }
    }

    protected function test_cashback_event()
    {
        echo "[TEST] 5. Cashback Grant on Order Paid\n";
        
        try {
            // Create premium user
            $premiumUser = User::factory()->create();
            $premiumUser->update(['premium_expires_at' => now()->addYear()]);
            
            // Ensure wallet exists
            MemberWallet::firstOrCreate(
                ['user_id' => $premiumUser->id],
                ['balance' => 0, 'locked_balance' => 0]
            );
            
            // Create premium membership
            PremiumMembership::factory()->create([
                'user_id' => $premiumUser->id,
                'status' => 'active',
                'started_at' => now(),
                'expires_at' => now()->addYear(),
            ]);
            
            // Create and mark order as paid
            $order = Order::factory()->create([
                'user_id' => $premiumUser->id,
                'total' => 100000,
            ]);
            
            // Dispatch OrderPaid event
            Event::dispatch(new OrderPaid($order));
            
            // Check ledger entry
            $ledger = MemberBalanceLedger::where('user_id', $premiumUser->id)
                ->where('source_type', 'premium_cashback')
                ->where('source_id', $order->id)
                ->first();
            
            $this->assertNotNull($ledger, "Cashback ledger created");
            $this->assertEqual($ledger->amount, 1000, "Cashback is 1% (1000)");  // 100000 * 0.01
            $this->assertEqual($ledger->type, 'credit', "Ledger type is credit");
            
            // Check wallet balance
            $wallet = $premiumUser->wallet()->first();
            $this->assertEqual($wallet->balance, 1000, "Wallet balance increased by 1000");
            
            echo "   ✓ Cashback event working\n\n";
        } catch (\Exception $e) {
            $this->fail("Cashback event failed: " . $e->getMessage());
        }
    }

    protected function test_multiple_orders()
    {
        echo "[TEST] 6. Multiple Orders Cashback\n";
        
        try {
            // Create premium user
            $premiumUser = User::factory()->create();
            $premiumUser->update(['premium_expires_at' => now()->addYear()]);
            
            // Ensure wallet exists
            MemberWallet::firstOrCreate(
                ['user_id' => $premiumUser->id],
                ['balance' => 0, 'locked_balance' => 0]
            );
            
            // Create premium membership
            PremiumMembership::factory()->create([
                'user_id' => $premiumUser->id,
                'status' => 'active',
                'started_at' => now(),
                'expires_at' => now()->addYear(),
            ]);
            
            // Create multiple orders
            $orders = Order::factory()->count(3)->create([
                'user_id' => $premiumUser->id,
                'total' => 100000,
            ]);
            
            // Dispatch events for each order
            foreach ($orders as $order) {
                Event::dispatch(new OrderPaid($order));
            }
            
            // Check total cashback
            $totalCashback = MemberBalanceLedger::where('user_id', $premiumUser->id)
                ->where('source_type', 'premium_cashback')
                ->sum('amount');
            
            $expectedTotal = (100000 * 0.01) * 3;
            $this->assertEqual($totalCashback, $expectedTotal, "Total cashback for 3 orders correct");
            
            // Check wallet
            $wallet = $premiumUser->wallet()->first();
            $this->assertEqual($wallet->balance, $expectedTotal, "Wallet balance correct");
            
            echo "   ✓ Multiple orders cashback working\n\n";
        } catch (\Exception $e) {
            $this->fail("Multiple orders test failed: " . $e->getMessage());
        }
    }

    // Helper methods
    protected function assertTrue($condition, $message)
    {
        if ($condition) {
            echo "   ✓ $message\n";
            $this->passCount++;
        } else {
            echo "   ✗ $message (expected true)\n";
            $this->fail($message);
        }
    }

    protected function assertFalse($condition, $message)
    {
        if (!$condition) {
            echo "   ✓ $message\n";
            $this->passCount++;
        } else {
            echo "   ✗ $message (expected false)\n";
            $this->fail($message);
        }
    }

    protected function assertEqual($actual, $expected, $message)
    {
        if ($actual == $expected) {
            echo "   ✓ $message\n";
            $this->passCount++;
        } else {
            echo "   ✗ $message (expected: $expected, got: $actual)\n";
            $this->failCount++;
        }
    }

    protected function assertNotNull($value, $message)
    {
        if ($value !== null) {
            echo "   ✓ $message\n";
            $this->passCount++;
        } else {
            echo "   ✗ $message (value is null)\n";
            $this->failCount++;
        }
    }

    protected function assertNull($value, $message)
    {
        if ($value === null) {
            echo "   ✓ $message\n";
            $this->passCount++;
        } else {
            echo "   ✗ $message (value is not null: $value)\n";
            $this->failCount++;
        }
    }

    protected function fail($message)
    {
        $this->failCount++;
    }

    protected function summary()
    {
        echo "==============================================================\n";
        echo "Test Results\n";
        echo "==============================================================\n";
        echo "✓ Passed: {$this->passCount}\n";
        echo "✗ Failed: {$this->failCount}\n";
        echo "==============================================================\n\n";
        
        if ($this->failCount === 0) {
            echo "✓ All tests passed!\n";
            exit(0);
        } else {
            echo "✗ Some tests failed\n";
            exit(1);
        }
    }
}

// Run tests
$suite = new PremiumMembershipTestSuite();
$suite->run();
