<?php

namespace Tests\Feature;

use App\Events\OrderPaid;
use App\Models\MemberBalanceLedger;
use App\Models\MemberWallet;
use App\Models\Order;
use App\Models\PremiumMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GrantPremiumCashbackTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        
        // Ensure user has wallet
        MemberWallet::firstOrCreate(
            ['user_id' => $this->user->id],
            ['balance' => 0, 'locked_balance' => 0]
        );

        // Create an order
        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'total' => 100000,
            'payment_status' => 'paid',
            'status' => 'completed',
        ]);
    }

    public function test_cashback_granted_for_premium_user(): void
    {
        // Make user premium
        $this->user->update(['premium_expires_at' => now()->addYear()]);

        // Create active premium membership
        PremiumMembership::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        // Fire OrderPaid event
        OrderPaid::dispatch($this->order);

        // Check if cashback was credited
        $expectedCashback = 100000 * 0.01; // 1%

        $ledger = MemberBalanceLedger::where('user_id', $this->user->id)
            ->where('source_type', 'premium_cashback')
            ->where('source_id', $this->order->id)
            ->first();

        $this->assertNotNull($ledger);
        $this->assertEquals($expectedCashback, (float) $ledger->amount);
        $this->assertEquals('credit', $ledger->type);

        // Check wallet balance updated
        $wallet = $this->user->wallet()->first();
        $this->assertEquals($expectedCashback, (float) $wallet->balance);
    }

    public function test_cashback_not_granted_for_non_premium_user(): void
    {
        // User is not premium
        $this->assertFalse($this->user->isPremium());

        // Fire OrderPaid event
        OrderPaid::dispatch($this->order);

        // Check no ledger entry created
        $ledger = MemberBalanceLedger::where('user_id', $this->user->id)
            ->where('source_type', 'premium_cashback')
            ->first();

        $this->assertNull($ledger);

        // Wallet balance should remain 0
        $wallet = $this->user->wallet()->first();
        $this->assertEquals(0, (float) $wallet->balance);
    }

    public function test_cashback_not_granted_for_expired_premium(): void
    {
        // Premium expired
        $this->user->update(['premium_expires_at' => now()->subDay()]);

        // Even with expired membership record
        PremiumMembership::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'expired',
            'started_at' => now()->subYear(),
            'expires_at' => now()->subDay(),
        ]);

        // Fire OrderPaid event
        OrderPaid::dispatch($this->order);

        // Check no ledger entry created (user not premium anymore)
        $ledger = MemberBalanceLedger::where('user_id' => $this->user->id)
            ->where('source_type', 'premium_cashback')
            ->first();

        $this->assertNull($ledger);
    }

    public function test_cashback_amount_correct(): void
    {
        // Make user premium
        $this->user->update(['premium_expires_at' => now()->addYear()]);

        PremiumMembership::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        // Create order with specific amount
        $testOrder = Order::factory()->create([
            'user_id' => $this->user->id,
            'total' => 250000,
            'payment_status' => 'paid',
            'status' => 'completed',
        ]);

        // Fire OrderPaid event
        OrderPaid::dispatch($testOrder);

        // Check cashback amount
        $ledger = MemberBalanceLedger::where('user_id' => $this->user->id)
            ->where('source_id', $testOrder->id)
            ->first();

        $expectedCashback = 250000 * 0.01; // Rp 2,500
        $this->assertEquals($expectedCashback, (float) $ledger->amount);
    }

    public function test_multiple_orders_multiple_cashbacks(): void
    {
        // Make user premium
        $this->user->update(['premium_expires_at' => now()->addYear()]);

        PremiumMembership::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        // Create and pay multiple orders
        $orders = Order::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'total' => 100000,
            'payment_status' => 'paid',
            'status' => 'completed',
        ]);

        foreach ($orders as $order) {
            OrderPaid::dispatch($order);
        }

        // Check total cashback
        $totalCashback = MemberBalanceLedger::where('user_id' => $this->user->id)
            ->where('source_type', 'premium_cashback')
            ->sum('amount');

        $expectedTotal = (100000 * 0.01) * 3; // 3 orders x 1%
        $this->assertEquals($expectedTotal, (float) $totalCashback);
    }
}
