<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Exceptions\Wallet\DuplicateTransactionException;
use App\Services\MemberWalletService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GrantPremiumCashback
{
    protected MemberWalletService $walletService;

    /**
     * Create the event listener.
     */
    public function __construct(MemberWalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPaid $event): void
    {
        $order = $event->order;
        $user = $order->user;

        // Skip if no user or user not premium
        if (!$user || !$user->isPremium()) {
            return;
        }

        // Calculate 1% cashback
        $cashbackAmount = (float) ($order->total * 0.01);

        if ($cashbackAmount <= 0) {
            return;
        }

        try {
            // Generate unique reference code for this cashback transaction
            $referenceCode = 'PREMIUM_CASHBACK_' . $order->id . '_' . Str::random(6);

            // Credit the cashback to user's wallet
            $this->walletService->credit(
                $user,
                $cashbackAmount,
                'premium_cashback',
                $order->id,
                "Premium cashback 1% from order #{$order->order_number}",
                $referenceCode
            );

            Log::info('Premium cashback granted', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'order_total' => $order->total,
                'cashback_amount' => $cashbackAmount,
            ]);
        } catch (DuplicateTransactionException $e) {
            Log::warning('Duplicate premium cashback transaction detected', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to grant premium cashback', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
