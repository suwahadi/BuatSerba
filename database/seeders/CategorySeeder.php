<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'parent_id' => null,
                'name' => 'Elektronik',
                'slug' => 'elektronik',
                'description' => 'Produk elektronik dan gadget',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'parent_id' => null,
                'name' => 'Fashion',
                'slug' => 'fashion',
                'description' => 'Pakaian dan aksesoris',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'parent_id' => null,
                'name' => 'Rumah Tangga',
                'slug' => 'rumah-tangga',
                'description' => 'Peralatan rumah tangga',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'parent_id' => null,
                'name' => 'Olahraga',
                'slug' => 'olahraga',
                'description' => 'Peralatan dan perlengkapan olahraga',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'parent_id' => null,
                'name' => 'Makanan & Minuman',
                'slug' => 'makanan-minuman',
                'description' => 'Produk makanan dan minuman',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'parent_id' => 1,
                'name' => 'Smartphone',
                'slug' => 'smartphone',
                'description' => 'Handphone dan aksesoris',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'parent_id' => 1,
                'name' => 'Laptop & Komputer',
                'slug' => 'laptop-komputer',
                'description' => 'Laptop, PC, dan aksesoris',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'parent_id' => 2,
                'name' => 'Pakaian Pria',
                'slug' => 'pakaian-pria',
                'description' => 'Fashion untuk pria',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'parent_id' => 2,
                'name' => 'Pakaian Wanita',
                'slug' => 'pakaian-wanita',
                'description' => 'Fashion untuk wanita',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'parent_id' => 3,
                'name' => 'Peralatan Dapur',
                'slug' => 'peralatan-dapur',
                'description' => 'Alat masak dan peralatan dapur',
                'is_active' => true,
                'sort_order' => 1,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
