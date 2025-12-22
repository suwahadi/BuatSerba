<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('BR-###')),
            'name' => fake()->company().' Branch',
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'province_id' => fake()->numberBetween(1, 34),
            'province_name' => fake()->state(),
            'city_id' => fake()->numberBetween(1, 500),
            'city_name' => fake()->city(),
            'city_type' => fake()->randomElement(['Kota', 'Kabupaten']),
            'subdistrict_id' => fake()->numberBetween(1, 1000),
            'subdistrict_name' => fake()->citySuffix(),
            'postal_code' => fake()->postcode(),
            'full_address' => fake()->address(),
            'is_active' => true,
            'priority' => fake()->numberBetween(1, 10),
        ];
    }
}
