<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Sku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_add_different_sku_items_to_cart()
    {
        // Create a product with multiple SKUs
        $product = Product::factory()->create(['name' => 'Test Product']);

        $sku1 = Sku::factory()->create([
            'product_id' => $product->id,
            'sku_code' => 'SKU-001',
            'selling_price' => 10000,
            'stock_quantity' => 100,
        ]);

        $sku2 = Sku::factory()->create([
            'product_id' => $product->id,
            'sku_code' => 'SKU-002',
            'selling_price' => 15000,
            'stock_quantity' => 100,
        ]);

        // Set up session
        session()->put('cart_session_id', session()->getId());
        $sessionId = session()->get('cart_session_id');

        // Add first SKU to cart
        CartItem::create([
            'session_id' => $sessionId,
            'user_id' => null,
            'product_id' => $product->id,
            'sku_id' => $sku1->id,
            'quantity' => 2,
            'price' => 10000,
        ]);

        // Add second SKU to cart
        CartItem::create([
            'session_id' => $sessionId,
            'user_id' => null,
            'product_id' => $product->id,
            'sku_id' => $sku2->id,
            'quantity' => 1,
            'price' => 15000,
        ]);

        // Assert both items exist in cart
        $cartItems = CartItem::where('session_id', $sessionId)->get();

        $this->assertCount(2, $cartItems);
        $this->assertTrue($cartItems->contains('sku_id', $sku1->id));
        $this->assertTrue($cartItems->contains('sku_id', $sku2->id));
    }

    /** @test */
    public function it_updates_quantity_when_adding_same_sku_to_cart()
    {
        $product = Product::factory()->create();
        $sku = Sku::factory()->create([
            'product_id' => $product->id,
            'sku_code' => 'SKU-001',
            'selling_price' => 10000,
            'stock_quantity' => 100,
        ]);

        session()->put('cart_session_id', session()->getId());
        $sessionId = session()->get('cart_session_id');

        // Add SKU to cart first time
        $cartItem = CartItem::create([
            'session_id' => $sessionId,
            'user_id' => null,
            'product_id' => $product->id,
            'sku_id' => $sku->id,
            'quantity' => 2,
            'price' => 10000,
        ]);

        // Simulate adding the same SKU again
        $existingItem = CartItem::where('sku_id', $sku->id)
            ->where(function ($query) use ($sessionId) {
                $query->where('session_id', $sessionId);
            })
            ->first();

        $this->assertNotNull($existingItem);
        $this->assertEquals($cartItem->id, $existingItem->id);

        // Update quantity
        $existingItem->quantity += 3;
        $existingItem->save();

        // Assert only one item exists with updated quantity
        $this->assertCount(1, CartItem::where('session_id', $sessionId)->get());
        $this->assertEquals(5, $existingItem->fresh()->quantity);
    }

    /** @test */
    public function it_correctly_fetches_cart_items_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();
        $sku1 = Sku::factory()->create([
            'product_id' => $product->id,
            'sku_code' => 'SKU-001',
        ]);
        $sku2 = Sku::factory()->create([
            'product_id' => $product->id,
            'sku_code' => 'SKU-002',
        ]);

        // Add items to cart for this user
        CartItem::create([
            'session_id' => null,
            'user_id' => $user->id,
            'product_id' => $product->id,
            'sku_id' => $sku1->id,
            'quantity' => 1,
            'price' => 10000,
        ]);

        CartItem::create([
            'session_id' => null,
            'user_id' => $user->id,
            'product_id' => $product->id,
            'sku_id' => $sku2->id,
            'quantity' => 2,
            'price' => 15000,
        ]);

        // Create a cart item for another user (should not be fetched)
        $otherUser = User::factory()->create();
        CartItem::create([
            'session_id' => null,
            'user_id' => $otherUser->id,
            'product_id' => $product->id,
            'sku_id' => $sku1->id,
            'quantity' => 5,
            'price' => 10000,
        ]);

        // Fetch cart items using the fixed query
        session()->put('cart_session_id', session()->getId());
        $sessionId = session()->get('cart_session_id');

        $cartItems = CartItem::where(function ($query) use ($sessionId) {
            $query->where('session_id', $sessionId);
            if (auth()->check()) {
                $query->orWhere('user_id', auth()->id());
            }
        })->get();

        // Should only get items for current user
        $this->assertCount(2, $cartItems);
        $this->assertTrue($cartItems->every(fn ($item) => $item->user_id === $user->id));
    }
}
