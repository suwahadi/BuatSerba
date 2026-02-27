<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReleaseExpiredOrderInventory extends Command
{
    protected $signature = 'orders:release-expired-inventory';

    protected $description = 'Release inventory for expired/cancelled orders that were created before observer was registered';

    public function handle()
    {
        $this->info('Starting inventory release for expired/cancelled orders...');

        $expiredOrders = Order::query()
            ->whereIn('status', ['expired', 'cancelled'])
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('No expired or cancelled orders found.');

            return self::SUCCESS;
        }

        $inventoryService = new InventoryService();
        $successCount = 0;
        $errorCount = 0;

        foreach ($expiredOrders as $order) {
            try {
                foreach ($order->items as $item) {
                    $inventoryService->release(
                        $order->branch_id,
                        $item->sku_id,
                        $item->quantity
                    );
                }

                $this->line("✓ Released inventory for order: {$order->order_number}");
                $successCount++;

                Log::info('Inventory released for ' . $order->status . ' order', [
                    'order_number' => $order->order_number,
                    'order_id' => $order->id,
                    'items_count' => $order->items->count(),
                ]);
            } catch (\Exception $e) {
                $this->error("✗ Failed to release inventory for order {$order->order_number}: {$e->getMessage()}");
                $errorCount++;

                Log::error('Failed to release inventory for ' . $order->status . ' order', [
                    'order_number' => $order->order_number,
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("Summary: {$successCount} succeeded, {$errorCount} failed");

        return self::SUCCESS;
    }
}
