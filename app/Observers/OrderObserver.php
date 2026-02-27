<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function updated(Order $order)
    {
        $statusesToRelease = ['expired', 'cancelled'];

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

                Log::info('Inventory released for ' . $order->status . ' order', [
                    'order_number' => $order->order_number,
                    'order_id' => $order->id,
                    'items_count' => $order->items->count(),
                ]);
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
