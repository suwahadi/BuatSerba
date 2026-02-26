<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For each product without any sku rows, create a default sku
        $products = DB::table('products')->select('id', 'slug', 'name', 'main_image')->get();

        foreach ($products as $p) {
            $has = DB::table('skus')->where('product_id', $p->id)->exists();
            if (! $has) {
                DB::table('skus')->insert([
                    'product_id' => $p->id,
                    'sku' => Str::upper(Str::slug($p->slug ?: $p->name ?: 'p').'-d'),
                    'base_price' => 0,
                    'selling_price' => 0,
                    'weight' => 0,
                    'length' => null,
                    'width' => null,
                    'height' => null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: do not remove created SKUs automatically.
    }
};
