<?php

namespace Tests\Unit;

use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashSaleModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_live_returns_true_when_active_and_in_window(): void
    {
        $sale = FlashSale::factory()->create([
            'is_active' => true,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHour(),
        ]);

        $this->assertTrue($sale->isLive());
    }

    public function test_is_live_returns_false_when_inactive(): void
    {
        $sale = FlashSale::factory()->inactive()->create();

        $this->assertFalse($sale->isLive());
    }

    public function test_is_live_returns_false_when_upcoming(): void
    {
        $sale = FlashSale::factory()->upcoming()->create();

        $this->assertFalse($sale->isLive());
    }

    public function test_is_live_returns_false_when_ended(): void
    {
        $sale = FlashSale::factory()->ended()->create();

        $this->assertFalse($sale->isLive());
    }

    public function test_active_scope_filters_live_sales_only(): void
    {
        FlashSale::factory()->create();
        FlashSale::factory()->upcoming()->create();
        FlashSale::factory()->ended()->create();
        FlashSale::factory()->inactive()->create();

        $this->assertSame(1, FlashSale::active()->count());
    }

    public function test_remaining_seconds_is_positive_while_live(): void
    {
        $sale = FlashSale::factory()->create([
            'ends_at' => now()->addMinutes(30),
        ]);

        $this->assertGreaterThan(0, $sale->remainingSeconds());
    }

    public function test_remaining_seconds_is_zero_when_ended(): void
    {
        $sale = FlashSale::factory()->ended()->create();

        $this->assertSame(0, $sale->remainingSeconds());
    }

    public function test_item_remaining_stock_accessor(): void
    {
        $item = FlashSaleItem::factory()->create([
            'stock_limit' => 20,
            'sold_count' => 5,
        ]);

        $this->assertSame(15, $item->remaining_stock);
    }

    public function test_item_is_sold_out_when_sold_count_reaches_limit(): void
    {
        $item = FlashSaleItem::factory()->soldOut()->create([
            'stock_limit' => 10,
        ]);

        $this->assertTrue($item->is_sold_out);
        $this->assertSame(0, $item->remaining_stock);
    }

    public function test_discount_percentage_is_rounded_integer(): void
    {
        $item = FlashSaleItem::factory()->create([
            'flash_price' => 25000,
            'original_price_snapshot' => 50000,
        ]);

        $this->assertSame(50, $item->discount_percentage);
    }

    public function test_discount_percentage_is_zero_when_flash_not_cheaper(): void
    {
        $item = FlashSaleItem::factory()->create([
            'flash_price' => 50000,
            'original_price_snapshot' => 50000,
        ]);

        $this->assertSame(0, $item->discount_percentage);
    }

    public function test_active_for_sku_returns_item_only_when_sale_is_live(): void
    {
        $sale = FlashSale::factory()->ended()->create();
        $item = FlashSaleItem::factory()->create(['flash_sale_id' => $sale->id]);

        $this->assertNull(FlashSaleItem::activeForSku((int) $item->sku_id));

        $liveSale = FlashSale::factory()->create();
        $liveItem = FlashSaleItem::factory()->create(['flash_sale_id' => $liveSale->id]);

        $this->assertNotNull(FlashSaleItem::activeForSku((int) $liveItem->sku_id));
    }
}
