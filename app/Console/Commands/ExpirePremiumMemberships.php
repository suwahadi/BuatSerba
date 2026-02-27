<?php

namespace App\Console\Commands;

use App\Models\PremiumMembership;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpirePremiumMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'premium:expire-memberships';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Expire premium memberships that have passed their expiry date and update user premium_expires_at';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting premium membership expiry check...');

        // Find all active memberships that have expired
        $expiredMemberships = PremiumMembership::where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        if ($expiredMemberships->isEmpty()) {
            $this->info('No expired premium memberships found.');
            return self::SUCCESS;
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($expiredMemberships as $membership) {
            try {
                // Update membership status to expired
                $membership->update(['status' => 'expired']);

                // Find if there's another active membership for this user
                $activeMembers = PremiumMembership::where('user_id', $membership->user_id)
                    ->where('status', 'active')
                    ->where('expires_at', '>', now())
                    ->first();

                // If no other active membership, clear premium_expires_at from user
                if (!$activeMembers) {
                    $membership->user->update(['premium_expires_at' => null]);
                }

                $this->line("✓ Expired membership #{$membership->id} for user #{$membership->user_id}");
                $successCount++;

                Log::info('Premium membership expired', [
                    'membership_id' => $membership->id,
                    'user_id' => $membership->user_id,
                    'expires_at' => $membership->expires_at,
                ]);
            } catch (\Exception $e) {
                $this->error("✗ Failed to expire membership #{$membership->id}: {$e->getMessage()}");
                $errorCount++;

                Log::error('Failed to expire premium membership', [
                    'membership_id' => $membership->id,
                    'user_id' => $membership->user_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("\n✓ Completed: {$successCount} memberships expired, {$errorCount} errors");

        return self::SUCCESS;
    }
}
