<?php

namespace Database\Factories;

use App\Models\FlashSale;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FlashSale>
 */
class FlashSaleFactory extends Factory
{
    protected $model = FlashSale::class;

    public function definition(): array
    {
        $name = 'Flash '.fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(4),
            'tagline' => 'Hari Ini Saja',
            'banner_image' => null,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHours(3),
            'is_active' => true,
            'sort' => 0,
        ];
    }

    public function upcoming(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->addHour(),
            'ends_at' => now()->addHours(4),
        ]);
    }

    public function ended(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->subDays(2),
            'ends_at' => now()->subDay(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
