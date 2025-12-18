<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sku;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sku>
 */
class SkuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $basePrice = fake()->numberBetween(50000, 5000000);
        $sellingPrice = $basePrice * 0.9; // 10% discount

        return [
            'product_id' => Product::factory(),
            'sku' => 'SKU-'.fake()->unique()->bothify('???-####'),
            'attributes' => [],
            'base_price' => $basePrice,
            'selling_price' => $sellingPrice,
            'wholesale_price' => $sellingPrice * 0.85,
            'wholesale_min_quantity' => 10,
            'stock_quantity' => fake()->numberBetween(0, 100),
            'weight' => fake()->numberBetween(100, 5000),
            'length' => fake()->numberBetween(10, 100),
            'width' => fake()->numberBetween(10, 100),
            'height' => fake()->numberBetween(10, 100),
            'is_active' => true,
            'pricing_tiers' => null,
            'use_dynamic_pricing' => false,
        ];
    }

    /**
     * Indicate that the SKU is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the SKU is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }

    /**
     * Indicate that the SKU has dynamic pricing.
     */
    public function withDynamicPricing(): static
    {
        return $this->state(function (array $attributes) {
            $basePrice = $attributes['selling_price'];

            return [
                'use_dynamic_pricing' => true,
                'pricing_tiers' => [
                    ['quantity' => 1, 'price' => $basePrice, 'label' => 'Eceran'],
                    ['quantity' => 10, 'price' => $basePrice * 0.9, 'label' => 'Grosir 10+'],
                    ['quantity' => 50, 'price' => $basePrice * 0.85, 'label' => 'Grosir 50+'],
                ],
            ];
        });
    }
}
