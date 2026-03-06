<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockOpnameItem>
 */
class StockOpnameItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $systemStock = fake()->numberBetween(0, 100);
        $physicalStock = fake()->numberBetween(0, 100);

        return [
            'stock_opname_id' => \App\Models\StockOpname::factory(),
            'sku_id' => \App\Models\Sku::factory(),
            'system_stock' => $systemStock,
            'physical_stock' => $physicalStock,
            'difference' => $physicalStock - $systemStock,
            'new_system_stock' => null,
            'is_adjusted' => false,
        ];
    }
}
