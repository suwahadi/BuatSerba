<?php

namespace Tests\Unit;

use App\Models\PremiumMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PremiumMembershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_premium_membership_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $membership = PremiumMembership::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($membership->user()->is($user));
    }

    public function test_is_active(): void
    {
        $user = User::factory()->create();
        
        $activeMembership = PremiumMembership::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'started_at' => now()->subDay(),
            'expires_at' => now()->addYear(),
        ]);

        $this->assertTrue($activeMembership->isActive());
    }

    public function test_is_not_active_when_pending(): void
    {
        $user = User::factory()->create();
        
        $membership = PremiumMembership::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'started_at' => null,
            'expires_at' => null,
        ]);

        $this->assertFalse($membership->isActive());
    }

    public function test_is_not_active_when_expired(): void
    {
        $user = User::factory()->create();
        
        $expiredMembership = PremiumMembership::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'started_at' => now()->subYear(),
            'expires_at' => now()->subDay(),
        ]);

        $this->assertFalse($expiredMembership->isActive());
    }

    public function test_is_expired(): void
    {
        $user = User::factory()->create();
        
        $expiredMembership = PremiumMembership::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'started_at' => now()->subYear(),
            'expires_at' => now()->subDay(),
        ]);

        $this->assertTrue($expiredMembership->isExpired());
    }

    public function test_is_not_expired_when_active(): void
    {
        $user = User::factory()->create();
        
        $membership = PremiumMembership::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'started_at' => now()->subDay(),
            'expires_at' => now()->addYear(),
        ]);

        $this->assertFalse($membership->isExpired());
    }

    public function test_days_remaining(): void
    {
        $user = User::factory()->create();
        
        $membership = PremiumMembership::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'started_at' => now()->subDay(),
            'expires_at' => now()->addDays(30),
        ]);

        $daysRemaining = $membership->daysRemaining();
        
        $this->assertIsInt($daysRemaining);
        $this->assertGreaterThan(28, $daysRemaining);
        $this->assertLessThanOrEqual(30, $daysRemaining);
    }

    public function test_days_remaining_null_when_not_active(): void
    {
        $user = User::factory()->create();
        
        $membership = PremiumMembership::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'started_at' => null,
            'expires_at' => null,
        ]);

        $this->assertNull($membership->daysRemaining());
    }

    public function test_user_is_premium(): void
    {
        $user = User::factory()->create();
        
        $this->assertFalse($user->isPremium());

        $user->update(['premium_expires_at' => now()->addYear()]);

        $this->assertTrue($user->refresh()->isPremium());
    }

    public function test_user_is_not_premium_when_expired(): void
    {
        $user = User::factory()->create([
            'premium_expires_at' => now()->subDay(),
        ]);

        $this->assertFalse($user->isPremium());
    }

    public function test_user_premium_memberships_relation(): void
    {
        $user = User::factory()->create();
        
        PremiumMembership::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $this->assertCount(3, $user->premiumMemberships);
    }

    public function test_user_active_premium_membership_relation(): void
    {
        $user = User::factory()->create();
        
        // Create active membership
        $activeMembership = PremiumMembership::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'started_at' => now()->subDay(),
            'expires_at' => now()->addYear(),
        ]);

        // Create pending membership
        PremiumMembership::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $this->assertTrue($user->activePremiumMembership()->first()->is($activeMembership));
    }
}
