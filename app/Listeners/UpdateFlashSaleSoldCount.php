<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\FlashSaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateFlashSaleSoldCount
{
    /**
     * Bump sold_count on each FlashSaleItem referenced by the paid order.
     * Idempotent — only fires when an OrderItem has flash_sale_item_id set.
     * Rolling back the increment on refund/cancel lives in
     * App\Services\OrderService::cancelOrder.
     */
    public function handle(OrderPaid $event): void
    {
        $order = $event->order;

        if (! $order || $order->payment_status !== 'paid') {
            return;
        }

        try {
            DB::transaction(function () use ($order) {
                foreach ($order->items as $orderItem) {
                    if (! $orderItem->flash_sale_item_id) {
                        continue;
                    }

                    $flashItem = FlashSaleItem::lockForUpdate()->find($orderItem->flash_sale_item_id);
                    if (! $flashItem) {
                        continue;
                    }

                    $flashItem->increment('sold_count', (int) $orderItem->quantity);
                }
            });
        } catch (\Throwable $e) {
            Log::error('Failed to update flash sale sold_count', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
