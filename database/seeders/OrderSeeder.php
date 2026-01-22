<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Sku;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $userIds = \App\Models\User::pluck('id')->toArray();

        $targetProductIds = range(1, 27);
        $totalOrders = 100;

        $validSkus = [];
        
        foreach ($targetProductIds as $pid) {
            $sku = Sku::where('product_id', $pid)->first();
            
            if (!$sku) {
                $product = Product::find($pid);
                if ($product) {
                    $sku = Sku::create([
                        'product_id' => $pid,
                        'sku' => 'SKU-' . $pid . '-' . Str::upper(Str::random(6)),
                        'unit_cost' => 45000,
                        'base_price' => 50000,
                        'selling_price' => 75000,
                        'weight' => 500,
                        'is_active' => true,
                    ]);
                }
            }
            
            if ($sku) {
                $validSkus[] = $sku;
            }
        }

        if (empty($validSkus)) {
            return;
        }

        for ($i = 0; $i < $totalOrders; $i++) {
            $orderDate = Carbon::now()->startOfMonth()->addDays(rand(0, Carbon::now()->day - 1));
            $hour = rand(8, 22);
            $minute = rand(0, 59);
            $second = rand(0, 59);
            $orderDate->setTime($hour, $minute, $second);

            $orderStatus = $faker->randomElement(['processing', 'shipped', 'delivered']);
            $paymentStatus = 'paid';

            $subtotal = 0;
            $orderItemsData = [];
            
            $itemCount = rand(1, 3);
            $selectedSkus = $faker->randomElements($validSkus, $itemCount);

            foreach ($selectedSkus as $sku) {
                $qty = rand(1, 5);
                $price = $sku->selling_price;
                $lineTotal = $qty * $price;
                $subtotal += $lineTotal;

                $orderItemsData[] = [
                    'product_id' => $sku->product_id,
                    'sku_id' => $sku->id,
                    'product_name' => $sku->product->name ?? 'Unknown Product',
                    'sku_code' => $sku->sku,
                    'sku_attributes' => json_encode($sku->attributes),
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $lineTotal,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ];
            }

            $shippingCost = rand(10, 50) * 1000;
            $grandTotal = $subtotal + $shippingCost;

            $paidAt = $paymentStatus === 'paid' ? $orderDate->copy()->addMinutes(rand(5, 120)) : null;

            $order = Order::create([
                'order_number' => 'BSR-' . Str::upper(Str::random(6)),
                'user_id' => !empty($userIds) ? $faker->randomElement($userIds) : null,
                'session_id' => Str::uuid(),
                
                'customer_name' => $faker->name,
                'customer_email' => $faker->email,
                'customer_phone' => $faker->phoneNumber,
                
                'shipping_province' => $faker->state,
                'shipping_city' => $faker->city,
                'shipping_district' => $faker->streetName,
                'shipping_postal_code' => $faker->postcode,
                'shipping_address' => $faker->address,
                
                'shipping_method' => 'courier',
                'shipping_service' => $faker->randomElement(['JNE Regular']),
                'shipping_cost' => $shippingCost,
                
                'payment_method' => $faker->randomElement(['bca_va', 'bri_va', 'bank_transfer']),
                'payment_status' => $paymentStatus,
                'paid_at' => $paidAt,
                
                'subtotal' => $subtotal,
                'service_fee' => 0,
                'discount' => 0,
                'total' => $grandTotal,
                
                'status' => $orderStatus,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            foreach ($orderItemsData as $item) {
                $item['order_id'] = $order->id;
                OrderItem::create($item);
            }
        }
    }
}
