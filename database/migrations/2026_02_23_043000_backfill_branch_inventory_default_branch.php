<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaultBranchId = 1;

        DB::table('skus')->orderBy('id')->chunkById(500, function ($skus) use ($defaultBranchId) {
            foreach ($skus as $sku) {
                $exists = DB::table('branch_inventory')
                    ->where('branch_id', $defaultBranchId)
                    ->where('sku_id', $sku->id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('branch_inventory')->insert([
                    'branch_id' => $defaultBranchId,
                    'sku_id' => $sku->id,
                    'quantity_available' => (int) ($sku->stock_quantity ?? 0),
                    'quantity_reserved' => 0,
                    'minimum_stock_level' => 0,
                    'reorder_point' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        DB::statement("UPDATE skus s SET s.stock_quantity = (SELECT COALESCE(SUM(bi.quantity_available),0) FROM branch_inventory bi WHERE bi.sku_id = s.id)");
    }

    public function down(): void
    {
        // no-op
    }
};
