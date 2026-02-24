<?php

namespace App\Observers;

use App\Models\Branch;
use App\Models\BranchInventory;
use App\Models\Sku;

class SkuObserver
{
    public function created(Sku $sku): void
    {
        $branches = Branch::query()
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();

        if ($branches->isEmpty()) {
            $branches = Branch::query()->whereKey(1)->get();
        }

        foreach ($branches as $branch) {
            BranchInventory::query()->firstOrCreate([
                'branch_id' => $branch->id,
                'sku_id' => $sku->id,
            ], [
                'quantity_available' => 0,
                'quantity_reserved' => 0,
                'minimum_stock_level' => 0,
                'reorder_point' => 0,
            ]);
        }
    }
}
