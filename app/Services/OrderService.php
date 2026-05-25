<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\FlashSaleItem;
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
            $branchId = (int) ($data['branch_id'] ?? 1);

            $sessionId = Session::get('cart_session_id');
            $cartItems = CartItem::with(['product', 'sku', 'flashSaleItem.flashSale'])
                ->where('session_id', $sessionId)
                ->when(auth()->check(), function ($query) {
                    $query->orWhere('user_id', auth()->id());
                })
                ->lockForUpdate()
                ->get();

            if ($cartItems->isEmpty()) {
                throw new Exception('Keranjang belanja kosong.');
            }

            foreach ($cartItems as $item) {
                $sku = Sku::lockForUpdate()->find($item->sku_id);

                if (! $sku) {
                    throw new Exception("Produk {$item->product->name} tidak ditemukan.");
                }

                $inventoryService = new InventoryService;
                $availableQty = $inventoryService->getAvailableQuantity($branchId, (int) $sku->id);
                if ($availableQty < $item->quantity) {
                    throw new Exception("Stok {$item->product->name} tidak mencukupi di cabang terpilih. Tersedia: {$availableQty}");
                }

                if ($item->flash_sale_item_id) {
                    $flashItem = FlashSaleItem::lockForUpdate()->find($item->flash_sale_item_id);

                    if (! $flashItem || ! $flashItem->flashSale || ! $flashItem->flashSale->isLive()) {
                        throw new Exception("Sesi Flash Sale untuk {$item->product->name} sudah berakhir. Silakan refresh keranjang.");
                    }

                    $available = (int) $flashItem->stock_limit - (int) $flashItem->sold_count;
                    if ($available < $item->quantity) {
                        throw new Exception("Kuota Flash Sale {$item->product->name} tidak mencukupi. Sisa: {$available}");
                    }
                }
            }

            $subtotal = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $shippingCost = $data['shipping_cost'] ?? 0;
            $serviceFee = $data['service_fee'] ?? 2000;
            $discount = $data['discount'] ?? 0;
            $total = $subtotal + $shippingCost + $serviceFee - $discount;

            $orderNumber = $this->generateOrderNumber();
            $paymentDeadline = $this->getExpirationTime();

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => auth()->id(),
                'session_id' => $sessionId,
                'branch_id' => $branchId,
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
                'payment_deadline' => $paymentDeadline,
                'subtotal' => $subtotal,
                'service_fee' => $serviceFee,
                'discount' => $discount,
                'total' => $total,
                'status' => 'pending',
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'sku_id' => $cartItem->sku_id,
                    'flash_sale_item_id' => $cartItem->flash_sale_item_id,
                    'product_name' => $cartItem->product->name,
                    'sku_code' => $cartItem->sku->sku,
                    'sku_attributes' => $cartItem->sku->attributes,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'subtotal' => $cartItem->price * $cartItem->quantity,
                ]);

                $inventoryService = new InventoryService;
                $inventoryService->reserve($branchId, (int) $cartItem->sku_id, (int) $cartItem->quantity);
            }

            CartItem::where('session_id', $sessionId)
                ->when(auth()->check(), function ($query) {
                    $query->orWhere('user_id', auth()->id());
                })
                ->delete();

            \App\Jobs\SendOrderCreatedEmail::dispatch($order);

            return $order;
        }, 5);
    }

    protected function generateOrderNumber(): string
    {
        do {
            $prefix = global_config('prefix_trx') ?? 'ORD-';
            $orderNumber = $prefix.strtoupper(substr(uniqid(), -6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    public function cancelOrder(Order $order, ?string $reason = null): void
    {
        if ($order->status === 'cancelled') {
            throw new Exception('Pesanan sudah dibatalkan.');
        }

        if ($order->status === 'shipped' || $order->status === 'delivered') {
            throw new Exception('Pesanan yang sudah dikirim tidak dapat dibatalkan.');
        }

        DB::transaction(function () use ($order, $reason) {
            $branchId = (int) ($order->branch_id ?? 1);
            $inventoryService = new InventoryService;
            foreach ($order->items as $item) {
                $inventoryService->release($branchId, (int) $item->sku_id, (int) $item->quantity);
            }

            // Rollback flash sale sold_count if this paid order is being cancelled/refunded.
            // Pending orders never incremented (listener fires only on OrderPaid), so safe-skip.
            if ($order->payment_status === 'paid') {
                foreach ($order->items as $item) {
                    if (! $item->flash_sale_item_id) {
                        continue;
                    }
                    $flashItem = FlashSaleItem::lockForUpdate()->find($item->flash_sale_item_id);
                    if (! $flashItem) {
                        continue;
                    }
                    $decrement = min((int) $flashItem->sold_count, (int) $item->quantity);
                    if ($decrement > 0) {
                        $flashItem->decrement('sold_count', $decrement);
                    }
                }
            }

            if ($order->payment_status === 'paid' && $order->user_id) {
                $payment = \App\Models\Payment::where('order_id', $order->id)
                    ->where('payment_gateway', 'member_balance')
                    ->first();

                if ($payment) {
                    $memberWalletService = new \App\Services\MemberWalletService();
                    $memberWalletService->releaseOrderLock(
                        $order->user_id,
                        $order->id,
                        'Pembatalan order #' . $order->order_number
                    );
                }
            }

            $order->cancel($reason);
        });
    }

    public function getExpirationTime()
    {
        $duration = (int) (global_config('expiration_time') ?? 1440);

        return now()->addMinutes($duration);
    }
}
