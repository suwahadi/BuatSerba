<?php

namespace Tests\Feature;

use App\Models\PremiumMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpirePremiumMembershipsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_expires_expired_memberships(): void
    {
        $user = User::factory()->create();

        // Create an expired membership
        $expiredMembership = PremiumMembership::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'started_at' => now()->subYear(),
            'expires_at' => now()->subDay(),
        ]);

        // User has expired premium
        $user->update(['premium_expires_at' => now()->subDay()]);

        // Run command
        $this->artisan('premium:expire-memberships')
            ->assertExitCode(0);

        // Check membership status changed to expired
        $this->assertDatabaseHas('premium_memberships', [
            'id' => $expiredMembership->id,
            'status' => 'expired',
        ]);
    }

    public function test_command_clears_user_premium_expires_at(): void
    {
        $user = User::factory()->create([
            'premium_expires_at' => now()->subDay(),
        ]);

        // Create expired membership
        PremiumMembership::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'started_at' => now()->subYear(),
            'expires_at' => now()->subDay(),
        ]);

        // Run command
        $this->artisan('premium:expire-memberships')
            ->assertExitCode(0);

        // Check user's premium_expires_at is cleared
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'premium_expires_at' => null,
        ]);
    }

    public function test_command_preserves_premium_with_multiple_memberships(): void
    {
        $user = User::factory()->create();

        // Create expired membership
        $expiredMembership = PremiumMembership::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'started_at' => now()->subYear(),
            'expires_at' => now()->subDay(),
        ]);

        // Create another active membership
        $activeMembership = PremiumMembership::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'started_at' => now()->subDay(),
            'expires_at' => now()->addYear(),
        ]);

        // User is premium from the active membership
        $user->update(['premium_expires_at' => now()->addYear()]);

        // Run command
        $this->artisan('premium:expire-memberships')
            ->assertExitCode(0);

        // Expired should be marked as expired
        $this->assertDatabaseHas('premium_memberships', [
            'id' => $expiredMembership->id,
            'status' => 'expired',
        ]);

        // But user still has premium from the active membership
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'premium_expires_at' => $activeMembership->expires_at,
        ]);
    }

    public function test_command_skips_non_expired_memberships(): void
    {
        $user = User::factory()->create();

        // Create active membership that hasn't expired
        $activeMembership = PremiumMembership::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'started_at' => now()->subDay(),
            'expires_at' => now()->addYear(),
        ]);

        $user->update(['premium_expires_at' => now()->addYear()]);

        // Run command
        $this->artisan('premium:expire-memberships')
            ->assertExitCode(0);

        // Check membership status still active
        $this->assertDatabaseHas('premium_memberships', [
            'id' => $activeMembership->id,
            'status' => 'active',
        ]);

        // User still premium
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'premium_expires_at' => $activeMembership->expires_at,
        ]);
    }

    public function test_command_handles_multiple_users(): void
    {
        // Create multiple users with expired memberships
        $users = User::factory()->count(3)->create();

        foreach ($users as $user) {
            PremiumMembership::factory()->create([
                'user_id' => $user->id,
                'status' => 'active',
                'started_at' => now()->subYear(),
                'expires_at' => now()->subDay(),
            ]);
            
            $user->update(['premium_expires_at' => now()->subDay()]);
        }

        // Run command
        $this->artisan('premium:expire-memberships')
            ->assertExitCode(0);

        // Check all memberships expired
        foreach ($users as $user) {
            $this->assertDatabaseHas('premium_memberships', [
                'user_id' => $user->id,
                'status' => 'expired',
            ]);

            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'premium_expires_at' => null,
            ]);
        }
    }

    public function test_command_only_affects_active_status(): void
    {
        $user = User::factory()->create();

        // Create pending membership (should not be touched)
        $pendingMembership = PremiumMembership::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'expires_at' => now()->subDay(),
        ]);

        // Run command
        $this->artisan('premium:expire-memberships')
            ->assertExitCode(0);

        // Check pending membership still pending
        $this->assertDatabaseHas('premium_memberships', [
            'id' => $pendingMembership->id,
            'status' => 'pending',
        ]);
    }

    public function test_command_outputs_results(): void
    {
        $user = User::factory()->create();

        PremiumMembership::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => 'active',
            'started_at' => now()->subYear(),
            'expires_at' => now()->subDay(),
        ]);

        $this->artisan('premium:expire-memberships')
            ->expectOutputToContain('Starting premium membership expiry check')
            ->expectOutputToContain('Completed')
            ->assertExitCode(0);
    }
}
