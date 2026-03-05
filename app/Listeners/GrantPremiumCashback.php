<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Exceptions\Wallet\DuplicateTransactionException;
use App\Models\MemberBalanceLedger;
use App\Services\MemberWalletService;
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

        if (! $user || ! $user->isPremium()) {
            return;
        }

        if (! in_array($order->status, ['processing', 'completed'])) {
            return;
        }

        if ($order->payment_status !== 'paid') {
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
            // Use unique reference code
            $referenceCode = 'cashback_'.$order->order_number;

            $this->walletService->credit(
                $user,
                $cashbackAmount,
                'premium_cashback',
                $order->id,
                "Cashback #{$order->order_number}",
                $referenceCode
            );

            // Log::info('Premium cashback granted', [
            //     'user_id' => $user->id,
            //     'order_id' => $order->id,
            //     'order_number' => $order->order_number,
            //     'order_status' => $order->status,
            //     'payment_status' => $order->payment_status,
            //     'cashback_amount' => $cashbackAmount,
            // ]);
        } catch (DuplicateTransactionException $e) {
            return;
        } catch (\Exception $e) {
            Log::error('Failed to grant premium cashback', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'order_status' => $order->status,
                'payment_status' => $order->payment_status,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
