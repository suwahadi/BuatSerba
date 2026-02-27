<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        $names = [
            'Friya M', 'Siti Amira', 'Agus Pratama', 'Rina Wati', 'Indra Wijaya', 'Sari Indah', 'Hendra Gunawan', 'Ratna Sari', 'Eko Kurniawan', 'Sri Wahyuni', 'Maya Putri', 'Rizky Firmansyah', 'Nina Herlina', 'Fajar Hidayat', 'Lina Marlina', 'Tono W', 'Yanti Suharti', 'Andi Saputra', 'Nurul Hidayah', 'Ahmad Rifai', 'Dedi Setiawan', 'Wulan Dari', 'Putra Bangsa', 'Cahya Kamila', 'Bayu Nugraha', 'Ima Juliani', 'Dipta Saputra', 'Kiki Triana', 'Ega Rusly',
        ];

        $comments = [
            'Barang bagus, pengiriman cepat.',
            'Kualitas oke, harga terjangkau.',
            'Sangat puas dengan produk ini.',
            'Recommended seller! Barang original.',
            'Packing rapi dan aman. Terima kasih.',
            'Barang sesuai deskripsi. Terima kasih.',
            'Mantap jiwa, produknya keren.',
            'Pengiriman kilat, barang mulus.',
            'Suka banget sama barangnya.',
            'Harga paling murah. Top markotop!',
            'Produk berkualitas, bakal order lagi.',
            'Respon penjual cepat dan ramah.',
            'Sesuai ekspektasi, tidak mengecewakan.',
            'Harga bersahabat, kualitas pejabat.',
            'Barang sampai dengan selamat hehehe.',
            'Pokoknya kalau belanja disini saja.',
        ];

        foreach ($products as $product) {
            // Shuffle names to get random unique reviewers
            $reviewers = collect($names)->shuffle()->take(3);

            foreach ($reviewers as $name) {
                // Find or create user
                $email = Str::slug($name).'@example.test'; // use .test to be safe
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => bcrypt('password'),
                        'email_verified_at' => now(),
                        'role' => 'customer',  // Add default role
                    ]
                );

                // Create dummy order
                $order = Order::create([
                    'order_number' => 'ORD-REV-'.Str::random(10),
                    'user_id' => $user->id,
                    'customer_name' => $name,
                    'customer_email' => $email,
                    'customer_phone' => '08123456789',
                    'shipping_province' => 'Jawa Barat',
                    'shipping_city' => 'Bandung',
                    'shipping_district' => 'Coblong',
                    'shipping_subdistrict' => 'Sekeloa',
                    'shipping_postal_code' => '40135',
                    'shipping_address' => 'Jl. Percobaan No. 123',
                    'shipping_method' => 'JNE',
                    'shipping_service' => 'REG',
                    'shipping_cost' => 10000,
                    'subtotal' => 100000,
                    'total' => 110000,
                    'status' => 'completed',
                    'payment_status' => 'paid',
                    'payment_method' => 'transfer',
                    'paid_at' => now()->subDays(rand(1, 30)),
                ]);

                // Create product review
                ProductReview::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'rating' => 5,
                    'review' => collect($comments)->random(),
                    'is_verified_purchase' => true,
                    'is_approved' => true,
                    'created_at' => now()->subDays(rand(0, 30)),
                    'updated_at' => now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }
}
