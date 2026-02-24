<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Services\InventoryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckOrderExpirationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $expiredPayments = Payment::query()
            ->where('transaction_status', 'pending')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->with('order')
            ->get();

        foreach ($expiredPayments as $payment) {
            try {
                DB::transaction(function () use ($payment) {
                    $payment->update([
                        'transaction_status' => 'expired',
                    ]);

                    if ($payment->order && $payment->order->status !== 'expired') {
                        $payment->order->update([
                            'status' => 'expired',
                            'payment_status' => 'expired',
                            'cancelled_at' => now(),
                            'cancellation_reason' => 'Payment expired',
                        ]);

                        $branchId = (int) ($payment->order->branch_id ?? 1);
                        $inventoryService = new InventoryService;
                        foreach ($payment->order->items as $item) {
                            $inventoryService->release($branchId, (int) $item->sku_id, (int) $item->quantity);
                        }
                    }
                });
            } catch (\Exception $e) {
                Log::error("Failed to expire order {$payment->order_id}: {$e->getMessage()}");
            }
        }
    }
}
