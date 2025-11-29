<?php

namespace Database\Seeders;

use App\Models\BranchInventory;
use Illuminate\Database\Seeder;

class BranchInventorySeeder extends Seeder
{
    public function run(): void
    {
        // Create inventory for all SKUs in all branches
        $branches = \App\Models\Branch::all();
        $skus = \App\Models\Sku::all();

        foreach ($branches as $branch) {
            foreach ($skus as $sku) {
                BranchInventory::create([
                    'branch_id' => $branch->id,
                    'sku_id' => $sku->id,
                    'quantity_available' => rand(10, 100),
                    'quantity_reserved' => rand(0, 10),
                    'minimum_stock_level' => 5,
                    'reorder_point' => 10,
                ]);
            }
        }
    }
}
