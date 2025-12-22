<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BranchInventory>
 */
class BranchInventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => \App\Models\Branch::factory(),
            'sku_id' => \App\Models\Sku::factory(),
            'quantity_available' => fake()->numberBetween(0, 500),
            'quantity_reserved' => fake()->numberBetween(0, 50),
            'minimum_stock_level' => fake()->numberBetween(5, 20),
            'reorder_point' => fake()->numberBetween(20, 50),
        ];
    }
}
