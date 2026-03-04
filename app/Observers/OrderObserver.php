<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\InventoryService;
use App\Events\OrderPaid;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function updated(Order $order)
    {
        $statusesToRelease = ['expired', 'cancelled'];

        if ($order->wasChanged('payment_status') && $order->payment_status === 'paid') {
            try {
                $inventoryService = new InventoryService();
                foreach ($order->items as $item) {
                    $inventoryService->commit(
                        (int) ($order->branch_id ?? 1),
                        (int) $item->sku_id,
                        (int) $item->quantity
                    );
                }

                OrderPaid::dispatch($order);
                // Log::info('OrderPaid event dispatched from observer', [
                //     'order_id' => $order->id,
                //     'order_number' => $order->order_number,
                // ]);
            } catch (\Exception $e) {
                Log::error('Failed to dispatch OrderPaid from observer', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($order->wasChanged('status') && in_array($order->status, $statusesToRelease)) {
            try {
                $inventoryService = new InventoryService();

                foreach ($order->items as $item) {
                    $inventoryService->release(
                        $order->branch_id,
                        $item->sku_id,
                        $item->quantity
                    );
                }

                // Log::info('Inventory released for ' . $order->status . ' order', [
                //     'order_number' => $order->order_number,
                //     'order_id' => $order->id,
                //     'items_count' => $order->items->count(),
                // ]);
            } catch (\Exception $e) {
                Log::error('Failed to release inventory for ' . $order->status . ' order', [
                    'order_number' => $order->order_number,
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
