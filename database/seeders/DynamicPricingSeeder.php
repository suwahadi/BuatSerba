<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Sku;
use Illuminate\Database\Seeder;

class DynamicPricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing products to use dynamic pricing
        Sku::whereNotNull('wholesale_price')->where('wholesale_min_quantity', '>', 0)->update([
            'use_dynamic_pricing' => true,
            'pricing_tiers' => [
                [
                    'quantity' => 1,
                    'price' => 0, // Will be set to selling_price
                    'label' => 'Eceran',
                ],
                [
                    'quantity' => 100, // Based on the requirement: buy more than 100 pcs
                    'price' => 0, // Will be set to wholesale_price
                    'label' => 'Grosir',
                ],
            ],
        ]);

        // Update the pricing tiers with actual prices
        Sku::where('use_dynamic_pricing', true)->each(function ($sku) {
            $pricingTiers = $sku->pricing_tiers;
            $pricingTiers[0]['price'] = $sku->selling_price;
            $pricingTiers[1]['price'] = $sku->wholesale_price;
            $sku->pricing_tiers = $pricingTiers;
            $sku->save();
        });

        // Create a sample product with more complex pricing tiers
        $product = Product::create([
            'category_id' => 1,
            'name' => 'Contoh Produk dengan Harga Dinamis',
            'slug' => 'contoh-produk-dinamis',
            'description' => 'Produk ini memiliki beberapa tingkatan harga berdasarkan jumlah pembelian',
            'short_description' => 'Produk dengan harga dinamis',
            'main_image' => null,
            'is_active' => true,
            'is_featured' => true,
        ]);

        $sku = new Sku([
            'sku' => 'PROD-DINAMIS-001',
            'base_price' => 100000,
            'selling_price' => 90000,
            'weight' => 500,
            'stock_quantity' => 500,
            'is_active' => true,
            'use_dynamic_pricing' => true,
            'pricing_tiers' => [
                [
                    'quantity' => 1,
                    'price' => 90000,
                    'label' => 'Eceran',
                ],
                [
                    'quantity' => 10,
                    'price' => 85000,
                    'label' => 'Reseller Kecil',
                ],
                [
                    'quantity' => 50,
                    'price' => 80000,
                    'label' => 'Reseller Menengah',
                ],
                [
                    'quantity' => 100,
                    'price' => 75000,
                    'label' => 'Grosir',
                ],
            ],
        ]);

        $product->skus()->save($sku);
    }
}
