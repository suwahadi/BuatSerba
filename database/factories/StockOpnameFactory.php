<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockOpname>
 */
class StockOpnameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'branch_id' => \App\Models\Branch::factory(),
            'opname_date' => now(),
            'notes' => null,
            'is_adjusted' => false,
            'adjusted_at' => null,
        ];
    }
}
