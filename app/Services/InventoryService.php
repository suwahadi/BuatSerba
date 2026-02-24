<?php

namespace App\Services;

use App\Models\BranchInventory;
use App\Models\Sku;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    private function runAtomic(callable $callback): void
    {
        if (DB::transactionLevel() > 0) {
            $callback();
            return;
        }

        DB::transaction(function () use ($callback) {
            $callback();
        }, 5);
    }

    public function getAvailableQuantity(int $branchId, int $skuId): int
    {
        $inv = BranchInventory::query()
            ->where('branch_id', $branchId)
            ->where('sku_id', $skuId)
            ->first();

        return (int) ($inv->quantity_available ?? 0);
    }

    public function reserve(int $branchId, int $skuId, int $quantity): void
    {
        if ($quantity <= 0) {
            return;
        }

        $this->runAtomic(function () use ($branchId, $skuId, $quantity) {
            $inv = BranchInventory::query()
                ->where('branch_id', $branchId)
                ->where('sku_id', $skuId)
                ->lockForUpdate()
                ->first();

            if (! $inv) {
                $inv = BranchInventory::create([
                    'branch_id' => $branchId,
                    'sku_id' => $skuId,
                    'quantity_available' => 0,
                    'quantity_reserved' => 0,
                    'minimum_stock_level' => 0,
                    'reorder_point' => 0,
                ]);

                $inv->refresh();
            }

            if ($inv->quantity_available < $quantity) {
                throw new \RuntimeException('Stok cabang tidak mencukupi.');
            }

            $inv->update([
                'quantity_available' => $inv->quantity_available - $quantity,
                'quantity_reserved' => $inv->quantity_reserved + $quantity,
            ]);

            $this->syncSkuAggregateStock($skuId);
        });
    }

    public function release(int $branchId, int $skuId, int $quantity): void
    {
        if ($quantity <= 0) {
            return;
        }

        $this->runAtomic(function () use ($branchId, $skuId, $quantity) {
            $inv = BranchInventory::query()
                ->where('branch_id', $branchId)
                ->where('sku_id', $skuId)
                ->lockForUpdate()
                ->first();

            if (! $inv) {
                return;
            }

            $releaseQty = min($quantity, (int) $inv->quantity_reserved);
            if ($releaseQty <= 0) {
                return;
            }

            $inv->update([
                'quantity_available' => $inv->quantity_available + $releaseQty,
                'quantity_reserved' => $inv->quantity_reserved - $releaseQty,
            ]);

            $this->syncSkuAggregateStock($skuId);
        });
    }

    public function commit(int $branchId, int $skuId, int $quantity): void
    {
        if ($quantity <= 0) {
            return;
        }

        $this->runAtomic(function () use ($branchId, $skuId, $quantity) {
            $inv = BranchInventory::query()
                ->where('branch_id', $branchId)
                ->where('sku_id', $skuId)
                ->lockForUpdate()
                ->first();

            if (! $inv) {
                return;
            }

            $commitQty = min($quantity, (int) $inv->quantity_reserved);
            if ($commitQty <= 0) {
                return;
            }

            $inv->update([
                'quantity_reserved' => $inv->quantity_reserved - $commitQty,
            ]);

            $this->syncSkuAggregateStock($skuId);
        });
    }

    public function syncSkuAggregateStock(int $skuId): void
    {
        $totalAvailable = (int) BranchInventory::query()
            ->where('sku_id', $skuId)
            ->sum('quantity_available');

        Sku::query()->whereKey($skuId)->update([
            'stock_quantity' => $totalAvailable,
        ]);
    }
}
