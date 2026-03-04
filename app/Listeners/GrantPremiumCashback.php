<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Exceptions\Wallet\DuplicateTransactionException;
use App\Models\MemberBalanceLedger;
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

        if (!$user || !$user->isPremium()) {
            return;
        }

        $cashbackAmount = (float) ($order->total * 0.01);

        if ($cashbackAmount <= 0) {
            return;
        }

        $existing = MemberBalanceLedger::where('user_id', $user->id)
            ->where('source_type', 'premium_cashback')
            ->where('source_id', $order->id)
            ->exists();

        if ($existing) {
            return;
        }

        try {
            $referenceCode = 'PREMIUM_CASHBACK_ORDER_' . $order->id;

            $this->walletService->credit(
                $user,
                $cashbackAmount,
                'premium_cashback',
                $order->id,
                "Premium cashback 1% from order #{$order->order_number}",
                $referenceCode
            );

            // Log::info('Premium cashback granted', [
            //     'user_id' => $user->id,
            //     'order_id' => $order->id,
            //     'order_number' => $order->order_number,
            //     'order_total' => $order->total,
            //     'cashback_amount' => $cashbackAmount,
            // ]);
        } catch (DuplicateTransactionException $e) {
            // 
            return;
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
