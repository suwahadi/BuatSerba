<?php

namespace Database\Factories;

use App\Models\PremiumMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PremiumMembershipFactory extends Factory
{
    protected $model = PremiumMembership::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'price' => 100000,
            'status' => $this->faker->randomElement(['pending', 'active', 'expired', 'cancelled']),
            'payment_method' => 'bank_transfer',
            'payment_proof_path' => null,
            'started_at' => null,
            'expires_at' => null,
        ];
    }

    public function pending(): self
    {
        return $this->state([
            'status' => 'pending',
            'started_at' => null,
            'expires_at' => null,
            'payment_proof_path' => null,
        ]);
    }

    public function active(): self
    {
        return $this->state([
            'status' => 'active',
            'started_at' => now()->subDay(),
            'expires_at' => now()->addYear(),
            'payment_proof_path' => 'premium-proof/sample.jpg',
        ]);
    }

    public function expired(): self
    {
        return $this->state([
            'status' => 'expired',
            'started_at' => now()->subYear()->subDay(),
            'expires_at' => now()->subDay(),
            'payment_proof_path' => 'premium-proof/sample.jpg',
        ]);
    }
}
