<?php

use App\Events\OrderPaid;
use App\Listeners\UpdateFlashSaleSoldCount;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Sku;

it('listener increments sold_count for order items with flash_sale_item_id', function () {
    $sale = FlashSale::factory()->create();
    $product = Product::factory()->create();
    $sku = Sku::factory()->create(['product_id' => $product->id]);
    $flashItem = FlashSaleItem::factory()->create([
        'flash_sale_id' => $sale->id,
        'sku_id' => $sku->id,
        'stock_limit' => 20,
        'sold_count' => 0,
    ]);

    $order = Order::factory()->create(['payment_status' => 'paid']);
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'sku_id' => $sku->id,
        'flash_sale_item_id' => $flashItem->id,
        'product_name' => $product->name,
        'sku_code' => $sku->sku,
        'quantity' => 3,
        'price' => 25000,
        'subtotal' => 75000,
    ]);

    $order->load('items');

    (new UpdateFlashSaleSoldCount)->handle(new OrderPaid($order));

    expect($flashItem->fresh()->sold_count)->toBe(3);
});

it('listener ignores order items without flash_sale_item_id', function () {
    $sale = FlashSale::factory()->create();
    $product = Product::factory()->create();
    $sku = Sku::factory()->create(['product_id' => $product->id]);
    $flashItem = FlashSaleItem::factory()->create([
        'flash_sale_id' => $sale->id,
        'sku_id' => $sku->id,
        'sold_count' => 5,
    ]);

    $order = Order::factory()->create(['payment_status' => 'paid']);
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'sku_id' => $sku->id,
        'flash_sale_item_id' => null,
        'product_name' => $product->name,
        'sku_code' => $sku->sku,
        'quantity' => 2,
        'price' => 50000,
        'subtotal' => 100000,
    ]);

    $order->load('items');

    (new UpdateFlashSaleSoldCount)->handle(new OrderPaid($order));

    expect($flashItem->fresh()->sold_count)->toBe(5);
});

it('listener skips orders that are not paid', function () {
    $sale = FlashSale::factory()->create();
    $product = Product::factory()->create();
    $sku = Sku::factory()->create(['product_id' => $product->id]);
    $flashItem = FlashSaleItem::factory()->create([
        'flash_sale_id' => $sale->id,
        'sku_id' => $sku->id,
        'sold_count' => 0,
    ]);

    $order = Order::factory()->create(['payment_status' => 'pending']);
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'sku_id' => $sku->id,
        'flash_sale_item_id' => $flashItem->id,
        'product_name' => $product->name,
        'sku_code' => $sku->sku,
        'quantity' => 2,
        'price' => 25000,
        'subtotal' => 50000,
    ]);

    $order->load('items');

    (new UpdateFlashSaleSoldCount)->handle(new OrderPaid($order));

    expect($flashItem->fresh()->sold_count)->toBe(0);
});
