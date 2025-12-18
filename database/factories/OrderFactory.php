<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'order_number' => 'ORD-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -6)),
            'user_id' => User::factory(),
            'session_id' => uniqid(),
            'customer_name' => $this->faker->name,
            'customer_email' => $this->faker->email,
            'customer_phone' => $this->faker->phoneNumber,
            'shipping_province' => $this->faker->state,
            'shipping_city' => $this->faker->city,
            'shipping_district' => $this->faker->streetName,
            'shipping_postal_code' => $this->faker->postcode,
            'shipping_address' => $this->faker->address,
            'shipping_method' => $this->faker->randomElement(['regular', 'express', 'same-day']),
            'shipping_cost' => $this->faker->randomElement([0, 25000, 50000, 75000]),
            'payment_method' => $this->faker->randomElement(['bank-transfer', 'e-wallet', 'credit-card', 'cod']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed']),
            'payment_deadline' => now()->addHours(24),
            'subtotal' => $this->faker->randomFloat(2, 100000, 10000000),
            'service_fee' => 2000,
            'discount' => 0,
            'total' => $this->faker->randomFloat(2, 100000, 10000000),
            'status' => $this->faker->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
        ];
    }
}
