<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\Sku;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::factory()->create();
        $sku = Sku::factory()->create(['product_id' => $product->id]);

        return [
            'order_id' => Order::factory(),
            'product_id' => $product->id,
            'sku_id' => $sku->id,
            'product_name' => $product->name,
            'sku_code' => $sku->sku,
            'sku_attributes' => ['size' => 'M', 'color' => 'Red'],
            'quantity' => $this->faker->numberBetween(1, 5),
            'price' => $this->faker->randomFloat(2, 10000, 500000),
            'subtotal' => $this->faker->randomFloat(2, 10000, 500000),
        ];
    }
}
