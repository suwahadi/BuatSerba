<?php

namespace Database\Factories;

use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Sku;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FlashSaleItem>
 */
class FlashSaleItemFactory extends Factory
{
    protected $model = FlashSaleItem::class;

    public function definition(): array
    {
        return [
            'flash_sale_id' => FlashSale::factory(),
            'sku_id' => Sku::factory(),
            'flash_price' => 25000,
            'original_price_snapshot' => 50000,
            'stock_limit' => 20,
            'sold_count' => 0,
            'sort' => 0,
        ];
    }

    public function soldOut(): static
    {
        return $this->state(fn (array $attrs) => [
            'sold_count' => $attrs['stock_limit'] ?? 20,
        ]);
    }
}
