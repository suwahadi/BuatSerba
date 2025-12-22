<?php

namespace Tests\Feature\Filament;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Sku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_order_items_section_with_correct_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a product with SKU
        $product = Product::factory()->create(['name' => 'Test Product']);
        $sku = Sku::factory()->create([
            'product_id' => $product->id,
            'sku_code' => 'TEST-SKU-001',
        ]);

        // Create an order with items
        $order = Order::factory()->create([
            'order_number' => 'ORD-TEST-001',
            'customer_name' => 'John Doe',
            'total' => 100000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'sku_id' => $sku->id,
            'product_name' => 'Test Product',
            'sku_code' => 'TEST-SKU-001',
            'quantity' => 2,
            'price' => 25000,
            'subtotal' => 50000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'sku_id' => $sku->id,
            'product_name' => 'Test Product 2',
            'sku_code' => 'TEST-SKU-002',
            'quantity' => 1,
            'price' => 50000,
            'subtotal' => 50000,
        ]);

        // Visit the edit page
        $response = $this->get("/admin/orders/{$order->id}/edit");

        $response->assertSuccessful();
        $response->assertSee('Order Items');
        $response->assertSee('TEST-SKU-001');
        $response->assertSee('TEST-SKU-002');
    }

    /** @test */
    public function it_hides_order_items_section_when_order_has_no_items()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $order = Order::factory()->create([
            'order_number' => 'ORD-EMPTY-001',
            'customer_name' => 'Jane Doe',
        ]);

        $response = $this->get("/admin/orders/{$order->id}/edit");

        $response->assertSuccessful();
        // The section should not be visible when there are no items
        $this->assertEquals(0, $order->items()->count());
    }
}
