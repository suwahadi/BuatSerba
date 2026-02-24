<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchInventory;
use App\Models\Sku;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchInventorySeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing branch inventory
        DB::table('branch_inventory')->delete();

        $branches = Branch::all();
        $skus = Sku::all();

        foreach ($branches as $branch) {
            foreach ($skus as $sku) {
                BranchInventory::create([
                    'branch_id' => $branch->id,
                    'sku_id' => $sku->id,
                    'quantity_available' => rand(1, 200),
                    'quantity_reserved' => 0,
                    'minimum_stock_level' => rand(5, 20),
                    'reorder_point' => rand(10, 30),
                ]);
            }
        }

        // Update skus.stock_quantity cache
        foreach ($skus as $sku) {
            $totalStock = BranchInventory::where('sku_id', $sku->id)->sum('quantity_available');
            $sku->update(['stock_quantity' => $totalStock]);
        }

        $this->command->info('Branch inventory seeded successfully!');
    }
}
