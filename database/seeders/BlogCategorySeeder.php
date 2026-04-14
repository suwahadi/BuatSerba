<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Seeder;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tips',
                'slug' => 'tips',
                'is_active' => true,
            ],
            [
                'name' => 'Motivasi',
                'slug' => 'motivasi',
                'is_active' => true,
            ],
            [
                'name' => 'Trend',
                'slug' => 'trend',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            BlogCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
