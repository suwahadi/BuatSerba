<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchInventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sku;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TestOrdersSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing orders, order_items, and payments
        DB::table('payments')->delete();
        DB::table('order_items')->delete();
        DB::table('orders')->delete();

        $branches = Branch::all();
        $skus = Sku::with('product')->get();
        $indonesianNames = [
            'Budi Santoso', 'Siti Nurhaliza', 'Ahmad Fauzi', 'Dewi Lestari', 'Eko Prasetyo',
            'Rina Wijaya', 'Joko Widodo', 'Maya Sari', 'Bambang Susilo', 'Fitri Handayani',
            'Rizki Ahmad', 'Nina Permata', 'Doni Prakoso', 'Ana Kartika', 'Hendra Kusuma',
            'Lisa Andriani', 'Fajar Nugroho', 'Titi Sumantri', 'Yudi Hermawan', 'Saskia Meutia'
        ];

        // Create 20 test orders
        for ($i = 0; $i < 20; $i++) {
            // Pick random branch
            $branch = $branches->random();
            
            // Pick 1-3 random SKUs from the same branch
            $selectedSkus = $skus->random(rand(1, 3));
            
            // Check stock availability
            $inventoryService = new InventoryService();
            $orderItems = [];
            $totalAmount = 0;
            
            foreach ($selectedSkus as $sku) {
                $availableStock = BranchInventory::where('branch_id', $branch->id)
                    ->where('sku_id', $sku->id)
                    ->value('quantity_available');
                
                if ($availableStock > 0) {
                    $quantity = min(rand(1, 3), $availableStock);
                    $price = $sku->price ?? $sku->product->price ?? 50000;
                    $subtotal = $price * $quantity;
                    
                    $orderItems[] = [
                        'sku_id' => $sku->id,
                        'product_id' => $sku->product_id,
                        'sku_code' => $sku->sku,
                        'product_name' => $sku->product->name,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ];
                    
                    $totalAmount += $subtotal;
                }
            }
            
            if (empty($orderItems)) {
                continue; // Skip if no items available
            }
            
            // Create order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'user_id' => User::inRandomOrder()->first()?->id,
                'session_id' => Str::random(32),
                'customer_name' => $indonesianNames[array_rand($indonesianNames)],
                'customer_email' => 'customer' . $i . '@example.com',
                'customer_phone' => '08' . rand(100000000, 999999999),
                'shipping_address' => 'Jl. Test No. ' . rand(1, 100) . ', Jakarta',
                'shipping_city' => 'Jakarta',
                'shipping_district' => 'Jakarta Pusat',
                'shipping_province' => 'DKI Jakarta',
                'shipping_postal_code' => rand(10000, 99999),
                'shipping_method' => 'regular',
                'subtotal' => $totalAmount,
                'shipping_cost' => rand(10000, 30000),
                'total' => $totalAmount + rand(10000, 30000),
                'payment_method' => ['bank_transfer', 'e_wallet', 'cash'][array_rand(['bank_transfer', 'e_wallet', 'cash'])],
                'payment_status' => 'paid',
                'status' => 'completed',
                'notes' => 'Test order ' . ($i + 1),
                'branch_id' => $branch->id,
                'paid_at' => now()->subDays(rand(0, 30)),
            ]);
            
            // Create order items
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'sku_id' => $item['sku_id'],
                    'product_id' => $item['product_id'],
                    'sku_code' => $item['sku_code'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);
                
                // Reserve and commit stock
                $inventoryService->reserve($branch->id, $item['sku_id'], $item['quantity']);
                $inventoryService->commit($branch->id, $item['sku_id'], $item['quantity']);
            }
            
            // Create payment
            Payment::create([
                'order_id' => $order->id,
                'payment_gateway' => 'midtrans',
                'transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
                'transaction_time' => $order->paid_at,
                'transaction_status' => 'settlement', // paid = settlement in Midtrans
                'payment_type' => $order->payment_method === 'cash' ? 'cstore' : 'bank_transfer',
                'gross_amount' => $order->total,
                'paid_at' => $order->paid_at,
                'expired_at' => $order->paid_at->copy()->addDay(),
            ]);
        }

        $this->command->info('Test orders seeded successfully!');
    }
}
