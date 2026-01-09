<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sku;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OrderService
{
    /**
     * Create order from cart with race condition protection
     *
     * @throws Exception
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Get cart items
            $sessionId = Session::get('cart_session_id');
            $cartItems = CartItem::with(['product', 'sku'])
                ->where('session_id', $sessionId)
                ->when(auth()->check(), function ($query) {
                    $query->orWhere('user_id', auth()->id());
                })
                ->lockForUpdate() // Lock rows to prevent race condition
                ->get();

            if ($cartItems->isEmpty()) {
                throw new Exception('Keranjang belanja kosong.');
            }

            // Validate stock availability with lock
            foreach ($cartItems as $item) {
                $sku = Sku::lockForUpdate()->find($item->sku_id);

                if (! $sku) {
                    throw new Exception("Produk {$item->product->name} tidak ditemukan.");
                }

                if ($sku->stock_quantity < $item->quantity) {
                    throw new Exception("Stok {$item->product->name} tidak mencukupi. Tersedia: {$sku->stock_quantity}");
                }
            }

            // Calculate totals
            $subtotal = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $shippingCost = $data['shipping_cost'] ?? 0;
            $serviceFee = $data['service_fee'] ?? 2000;
            $discount = $data['discount'] ?? 0;
            $total = $subtotal + $shippingCost + $serviceFee - $discount;

            // Generate unique order number
            $orderNumber = $this->generateOrderNumber();

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => auth()->id(),
                'session_id' => $sessionId,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'shipping_province' => $data['shipping_province'],
                'shipping_city' => $data['shipping_city'],
                'shipping_district' => $data['shipping_district'],
                'shipping_subdistrict' => $data['shipping_subdistrict'] ?? null,
                'shipping_postal_code' => $data['shipping_postal_code'],
                'shipping_address' => $data['shipping_address'],
                'shipping_method' => $data['shipping_method'],
                'shipping_cost' => $shippingCost,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'payment_deadline' => now()->addHours(24), // 24 hours to pay
                'subtotal' => $subtotal,
                'service_fee' => $serviceFee,
                'discount' => $discount,
                'total' => $total,
                'status' => 'pending',
            ]);

            // Create order items and reduce stock
            foreach ($cartItems as $cartItem) {
                // Create order item with snapshot
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'sku_id' => $cartItem->sku_id,
                    'product_name' => $cartItem->product->name,
                    'sku_code' => $cartItem->sku->sku,
                    'sku_attributes' => $cartItem->sku->attributes,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'subtotal' => $cartItem->price * $cartItem->quantity,
                ]);

                // Reduce stock (with lock already acquired)
                $sku = Sku::lockForUpdate()->find($cartItem->sku_id);
                $sku->decrement('stock_quantity', $cartItem->quantity);
            }

            // Clear cart
            CartItem::where('session_id', $sessionId)
                ->when(auth()->check(), function ($query) {
                    $query->orWhere('user_id', auth()->id());
                })
                ->delete();

            // Dispatch email notification job
            \App\Jobs\SendOrderCreatedEmail::dispatch($order);

            return $order;
        }, 5); // Retry 5 times if deadlock occurs
    }

    /**
     * Generate unique order number
     */
    protected function generateOrderNumber(): string
    {
        do {
            $prefix = global_config('prefix_trx') ?? 'ORD-';
            $orderNumber = $prefix.strtoupper(substr(uniqid(), -6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Cancel order and restore stock
     *
     * @throws Exception
     */
    public function cancelOrder(Order $order, ?string $reason = null): void
    {
        if ($order->status === 'cancelled') {
            throw new Exception('Pesanan sudah dibatalkan.');
        }

        if ($order->status === 'shipped' || $order->status === 'delivered') {
            throw new Exception('Pesanan yang sudah dikirim tidak dapat dibatalkan.');
        }

        DB::transaction(function () use ($order, $reason) {
            // Restore stock
            foreach ($order->items as $item) {
                $sku = Sku::lockForUpdate()->find($item->sku_id);
                if ($sku) {
                    $sku->increment('stock_quantity', $item->quantity);
                }
            }

            // Cancel order
            $order->cancel($reason);
        });
    }
}
