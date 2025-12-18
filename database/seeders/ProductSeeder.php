<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category_id' => 6, // Smartphone
                'name' => 'Samsung Galaxy S23',
                'slug' => 'samsung-galaxy-s23',
                'description' => 'Smartphone flagship Samsung dengan kamera 200MP dan layar Dynamic AMOLED 2X 6.1 inch',
                'short_description' => 'Smartphone flagship Samsung terbaru',
                'main_image' => 'https://indodana-web.imgix.net/assets/samsung-galaxy-s23-lavender-thumbnail.png',
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Samsung Galaxy S23 - BuatSerba',
                'meta_description' => 'Beli Samsung Galaxy S23 original dengan harga terbaik',
            ],
            [
                'category_id' => 6,
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'description' => 'iPhone terbaru dengan chip A17 Pro dan kamera 48MP',
                'short_description' => 'iPhone 15 Pro dengan teknologi terkini',
                'main_image' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=500',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_id' => 7, // Laptop
                'name' => 'ASUS ROG Zephyrus G14',
                'slug' => 'asus-rog-zephyrus-g14',
                'description' => 'Laptop gaming premium dengan AMD Ryzen 9 dan RTX 4060',
                'short_description' => 'Laptop gaming portabel dan powerful',
                'main_image' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=500',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_id' => 7,
                'name' => 'MacBook Air M3',
                'slug' => 'macbook-air-m3',
                'description' => 'MacBook Air dengan chip M3, 8GB RAM, 256GB SSD',
                'short_description' => 'MacBook Air tipis dan ringan',
                'main_image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=500',
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'category_id' => 8, // Pakaian Pria
                'name' => 'Kemeja Batik Pria Premium',
                'slug' => 'kemeja-batik-pria-premium',
                'description' => 'Kemeja batik dengan motif modern, bahan katun halus',
                'short_description' => 'Kemeja batik pria berkualitas',
                'main_image' => 'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?w=500',
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'category_id' => 8,
                'name' => 'Polo Shirt Pria',
                'slug' => 'polo-shirt-pria',
                'description' => 'Polo shirt casual dengan bahan cotton combed 30s',
                'short_description' => 'Polo shirt nyaman untuk sehari-hari',
                'main_image' => 'https://images.unsplash.com/photo-1581655353564-df123a1eb820?w=500',
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'category_id' => 9, // Pakaian Wanita
                'name' => 'Dress Wanita Elegant',
                'slug' => 'dress-wanita-elegant',
                'description' => 'Dress wanita dengan desain elegan untuk acara formal',
                'short_description' => 'Dress elegan untuk wanita',
                'main_image' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=500',
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'category_id' => 10, // Peralatan Dapur
                'name' => 'Rice Cooker Digital 2 Liter',
                'slug' => 'rice-cooker-digital-2-liter',
                'description' => 'Rice cooker digital dengan 12 menu masak otomatis',
                'short_description' => 'Rice cooker praktis dan modern',
                'main_image' => 'https://images.unsplash.com/photo-1585515320310-259814833e62?w=500',
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'category_id' => 10,
                'name' => 'Blender 1.5 Liter',
                'slug' => 'blender-15-liter',
                'description' => 'Blender dengan motor 500W dan pisau stainless steel',
                'short_description' => 'Blender powerful untuk smoothie',
                'main_image' => 'https://images.unsplash.com/photo-1570222094114-d054a817eb1c?w=500',
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'category_id' => 4, // Olahraga
                'name' => 'Sepatu Lari Nike Air Zoom',
                'slug' => 'sepatu-lari-nike-air-zoom',
                'description' => 'Sepatu lari dengan teknologi Air Zoom untuk kenyamanan maksimal',
                'short_description' => 'Sepatu lari Nike premium',
                'main_image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500',
                'is_active' => true,
                'is_featured' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
