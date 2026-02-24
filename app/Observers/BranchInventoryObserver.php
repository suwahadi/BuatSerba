<?php

namespace App\Observers;

use App\Models\BranchInventory;
use App\Services\InventoryService;

class BranchInventoryObserver
{
    public function saved(BranchInventory $branchInventory): void
    {
        $inventoryService = new InventoryService;
        $inventoryService->syncSkuAggregateStock((int) $branchInventory->sku_id);
    }

    public function deleted(BranchInventory $branchInventory): void
    {
        $inventoryService = new InventoryService;
        $inventoryService->syncSkuAggregateStock((int) $branchInventory->sku_id);
    }
}
