<?php

namespace Database\Seeders;

use App\Models\Sku;
use Illuminate\Database\Seeder;

class SkuSeeder extends Seeder
{
    public function run(): void
    {
        $skus = [
            // Samsung Galaxy S23 variants
            [
                'product_id' => 1,
                'sku' => 'SGS23-BLK-256',
                'attributes' => ['Warna' => 'Phantom Black', 'Kapasitas' => '256GB'],
                'base_price' => 12000000,
                'selling_price' => 11500000,
                'stock_quantity' => 15,
                'weight' => 200,
                'length' => 15,
                'width' => 7.5,
                'height' => 0.8,
                'is_active' => true,
            ],
            [
                'product_id' => 1,
                'sku' => 'SGS23-LAV-256',
                'attributes' => ['Warna' => 'Lavender', 'Kapasitas' => '256GB'],
                'base_price' => 12000000,
                'selling_price' => 11500000,
                'stock_quantity' => 12,
                'weight' => 200,
                'length' => 15,
                'width' => 7.5,
                'height' => 0.8,
                'is_active' => true,
            ],
            [
                'product_id' => 1,
                'sku' => 'SGS23-BLK-512',
                'attributes' => ['Warna' => 'Phantom Black', 'Kapasitas' => '512GB'],
                'base_price' => 14000000,
                'selling_price' => 13500000,
                'stock_quantity' => 8,
                'weight' => 200,
                'length' => 15,
                'width' => 7.5,
                'height' => 0.8,
                'is_active' => true,
            ],
            // iPhone 15 Pro
            [
                'product_id' => 2,
                'sku' => 'IPH15P-BLU-256',
                'attributes' => ['Warna' => 'Blue Titanium', 'Kapasitas' => '256GB'],
                'base_price' => 18000000,
                'selling_price' => 17500000,
                'stock_quantity' => 10,
                'weight' => 220,
                'length' => 15.5,
                'width' => 7.8,
                'height' => 0.85,
                'is_active' => true,
            ],
            // ASUS ROG
            [
                'product_id' => 3,
                'sku' => 'ASUS-G14-GRY',
                'attributes' => ['Warna' => 'Eclipse Gray'],
                'base_price' => 25000000,
                'selling_price' => 24000000,
                'stock_quantity' => 5,
                'weight' => 1800,
                'length' => 35,
                'width' => 25,
                'height' => 2.5,
                'is_active' => true,
            ],
            // MacBook Air
            [
                'product_id' => 4,
                'sku' => 'MBA-M3-SLV-256',
                'attributes' => ['Warna' => 'Silver', 'Kapasitas' => '256GB'],
                'base_price' => 16000000,
                'selling_price' => 15500000,
                'stock_quantity' => 7,
                'weight' => 1300,
                'length' => 30.5,
                'width' => 21.5,
                'height' => 1.2,
                'is_active' => true,
            ],
            // Kemeja Batik
            [
                'product_id' => 5,
                'sku' => 'BTK-M-BLU',
                'attributes' => ['Ukuran' => 'M', 'Warna' => 'Biru'],
                'base_price' => 250000,
                'selling_price' => 225000,
                'stock_quantity' => 25,
                'weight' => 300,
                'length' => 30,
                'width' => 25,
                'height' => 2,
                'is_active' => true,
            ],
            // Polo Shirt
            [
                'product_id' => 6,
                'sku' => 'POLO-L-WHT',
                'attributes' => ['Ukuran' => 'L', 'Warna' => 'Putih'],
                'base_price' => 150000,
                'selling_price' => 135000,
                'stock_quantity' => 30,
                'weight' => 250,
                'length' => 28,
                'width' => 22,
                'height' => 2,
                'is_active' => true,
            ],
            // Dress
            [
                'product_id' => 7,
                'sku' => 'DRS-M-BLK',
                'attributes' => ['Ukuran' => 'M', 'Warna' => 'Hitam'],
                'base_price' => 350000,
                'selling_price' => 315000,
                'stock_quantity' => 20,
                'weight' => 400,
                'length' => 32,
                'width' => 26,
                'height' => 3,
                'is_active' => true,
            ],
            // Rice Cooker
            [
                'product_id' => 8,
                'sku' => 'RC-2L-WHT',
                'attributes' => ['Warna' => 'Putih'],
                'base_price' => 750000,
                'selling_price' => 675000,
                'stock_quantity' => 18,
                'weight' => 3000,
                'length' => 30,
                'width' => 30,
                'height' => 25,
                'is_active' => true,
            ],
            // Blender
            [
                'product_id' => 9,
                'sku' => 'BLD-15L-RED',
                'attributes' => ['Warna' => 'Merah'],
                'base_price' => 450000,
                'selling_price' => 405000,
                'stock_quantity' => 22,
                'weight' => 2500,
                'length' => 25,
                'width' => 20,
                'height' => 35,
                'is_active' => true,
            ],
            // Sepatu Nike
            [
                'product_id' => 10,
                'sku' => 'NK-RUN-42-BLK',
                'attributes' => ['Ukuran' => '42', 'Warna' => 'Hitam'],
                'base_price' => 1500000,
                'selling_price' => 1350000,
                'stock_quantity' => 14,
                'weight' => 800,
                'length' => 33,
                'width' => 22,
                'height' => 12,
                'is_active' => true,
            ],
        ];

        foreach ($skus as $sku) {
            Sku::create($sku);
        }
    }
}
