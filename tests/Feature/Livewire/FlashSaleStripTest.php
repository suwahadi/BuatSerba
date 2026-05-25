<?php

use App\Livewire\Components\FlashSaleStrip;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Product;
use App\Models\Sku;
use Livewire\Livewire;

it('renders nothing when there is no active flash sale', function () {
    Livewire::test(FlashSaleStrip::class)
        ->assertStatus(200)
        ->assertDontSee('Flash Sale')
        ->assertDontSee('flash-countdown');
});

it('renders nothing when active sale has no items', function () {
    FlashSale::factory()->create();

    Livewire::test(FlashSaleStrip::class)
        ->assertStatus(200)
        ->assertDontSee('flash-countdown');
});

it('renders strip header and product card when sale is live', function () {
    $sale = FlashSale::factory()->create([
        'name' => 'Promo Mei',
        'tagline' => 'Hari Ini Saja',
        'ends_at' => now()->addHours(2),
    ]);

    $product = Product::factory()->create(['name' => 'Polo Misty Grey']);
    $sku = Sku::factory()->create(['product_id' => $product->id, 'selling_price' => 50000]);
    FlashSaleItem::factory()->create([
        'flash_sale_id' => $sale->id,
        'sku_id' => $sku->id,
        'flash_price' => 25000,
        'original_price_snapshot' => 50000,
        'stock_limit' => 20,
        'sold_count' => 8,
    ]);

    Livewire::test(FlashSaleStrip::class)
        ->assertStatus(200)
        ->assertSee('Promo Mei')
        ->assertSee('flash-countdown', escape: false)
        ->assertSee('Polo Misty Grey')
        ->assertSee('Rp 25.000');
});

it('hides ended sales even when is_active is true', function () {
    $sale = FlashSale::factory()->ended()->create();
    $product = Product::factory()->create();
    $sku = Sku::factory()->create(['product_id' => $product->id]);
    FlashSaleItem::factory()->create([
        'flash_sale_id' => $sale->id,
        'sku_id' => $sku->id,
    ]);

    Livewire::test(FlashSaleStrip::class)
        ->assertStatus(200)
        ->assertDontSee('flash-countdown', escape: false);
});
