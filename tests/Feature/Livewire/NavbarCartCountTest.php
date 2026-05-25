<?php

use App\Livewire\Home;
use App\Models\CartItem;
use App\Models\Sku;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;

// ---------- Render-only tests (don't need real cart data) ----------

it('hides cart badge when cartCount is zero', function () {
    $html = view('components.navbar', ['cartCount' => 0])->render();

    expect($html)->toContain('aria-label="Keranjang"')
        ->and($html)->not->toContain('aria-label="Keranjang (');
});

it('renders cart badge with the passed count', function () {
    $html = view('components.navbar', ['cartCount' => 3])->render();

    expect($html)->toContain('Keranjang (3 item)')
        ->and($html)->toContain('>3<');
});

it('clamps badge to 99+ when count exceeds 99', function () {
    $html = view('components.navbar', ['cartCount' => 150])->render();

    expect($html)->toContain('99+')
        ->and($html)->not->toContain('>150<');
});

it('uses smaller badge dimensions per design spec', function () {
    $html = view('components.navbar', ['cartCount' => 1])->render();

    expect($html)->toContain('min-w-[14px] h-[14px]')
        ->and($html)->toContain('text-[9px]');
});

// ---------- Auto-compute integration tests (real DB) ----------

it('shows no cart badge when cart is empty (Home page)', function () {
    Livewire::test(Home::class)
        ->assertStatus(200)
        ->assertDontSee('aria-label="Keranjang (', false);
});

it('auto-renders cart badge on home page for guest with session cart', function () {
    Session::put('cart_session_id', 'test-session-abc');

    $sku = Sku::factory()->create();
    CartItem::create([
        'session_id' => 'test-session-abc',
        'product_id' => $sku->product_id,
        'sku_id' => $sku->id,
        'quantity' => 2,
        'price' => 25000,
    ]);
    CartItem::create([
        'session_id' => 'test-session-abc',
        'product_id' => $sku->product_id,
        'sku_id' => $sku->id,
        'quantity' => 1,
        'price' => 35000,
    ]);

    Livewire::test(Home::class)
        ->assertStatus(200)
        ->assertSeeHtml('aria-label="Keranjang (2 item)"');
});

it('auto-renders cart badge on home page for authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $sku = Sku::factory()->create();
    CartItem::create([
        'session_id' => 'unrelated',
        'user_id' => $user->id,
        'product_id' => $sku->product_id,
        'sku_id' => $sku->id,
        'quantity' => 1,
        'price' => 10000,
    ]);

    Livewire::test(Home::class)
        ->assertStatus(200)
        ->assertSeeHtml('aria-label="Keranjang (1 item)"');
});

it('respects explicit cartCount over auto-compute', function () {
    // Even if DB has cart records under this session, an explicit cartCount must win.
    Session::put('cart_session_id', 'should-be-ignored');

    $sku = Sku::factory()->create();
    CartItem::create([
        'session_id' => 'should-be-ignored',
        'product_id' => $sku->product_id,
        'sku_id' => $sku->id,
        'quantity' => 1,
        'price' => 5000,
    ]);

    $html = view('components.navbar', ['cartCount' => 5])->render();

    expect($html)->toContain('Keranjang (5 item)');
});
