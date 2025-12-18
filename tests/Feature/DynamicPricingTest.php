<?php

namespace Tests\Feature;

use App\Models\Sku;
use Tests\TestCase;

class DynamicPricingTest extends TestCase
{
    /** @test */
    public function it_can_get_price_based_on_quantity_tiers()
    {
        // Create a SKU with dynamic pricing
        $sku = new Sku([
            'selling_price' => 10000,
            'wholesale_price' => 8000,
            'wholesale_min_quantity' => 100,
            'use_dynamic_pricing' => true,
            'pricing_tiers' => [
                [
                    'quantity' => 1,
                    'price' => 10000,
                    'label' => 'Eceran',
                ],
                [
                    'quantity' => 100,
                    'price' => 8000,
                    'label' => 'Grosir',
                ],
            ],
        ]);

        // Test retail price (quantity < 100)
        $this->assertEquals(10000, $sku->getPriceForQuantity(50));

        // Test wholesale price (quantity >= 100)
        $this->assertEquals(8000, $sku->getPriceForQuantity(150));
    }

    /** @test */
    public function it_falls_back_to_existing_wholesale_logic_when_dynamic_pricing_is_disabled()
    {
        // Create a SKU without dynamic pricing
        $sku = new Sku([
            'selling_price' => 10000,
            'wholesale_price' => 8000,
            'wholesale_min_quantity' => 100,
            'use_dynamic_pricing' => false,
        ]);

        // Test retail price (quantity < 100)
        $this->assertEquals(10000, $sku->getPriceForQuantity(50));

        // Test wholesale price (quantity >= 100)
        $this->assertEquals(8000, $sku->getPriceForQuantity(150));
    }

    /** @test */
    public function it_can_get_pricing_tiers_for_display()
    {
        // Create a SKU with dynamic pricing
        $sku = new Sku([
            'selling_price' => 10000,
            'wholesale_price' => 8000,
            'wholesale_min_quantity' => 100,
            'use_dynamic_pricing' => true,
            'pricing_tiers' => [
                [
                    'quantity' => 1,
                    'price' => 10000,
                    'label' => 'Eceran',
                ],
                [
                    'quantity' => 100,
                    'price' => 8000,
                    'label' => 'Grosir',
                ],
            ],
        ]);

        $tiers = $sku->getPricingTiersForDisplay();
        $this->assertCount(2, $tiers);
        $this->assertEquals(10000, $tiers[0]['price']);
        $this->assertEquals(8000, $tiers[1]['price']);
    }
}
