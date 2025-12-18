<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'attributes',
        'base_price',
        'selling_price',
        'wholesale_price',
        'wholesale_min_quantity',
        'stock_quantity',
        'weight',
        'length',
        'width',
        'height',
        'is_active',
        'pricing_tiers',
        'use_dynamic_pricing',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'attributes' => 'array',
            'base_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'wholesale_price' => 'decimal:2',
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'pricing_tiers' => 'array',
            'use_dynamic_pricing' => 'boolean',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branchInventory()
    {
        return $this->hasMany(BranchInventory::class);
    }

    /**
     * Get price based on quantity
     */
    public function getPriceForQuantity(int $quantity): float
    {
        // If dynamic pricing is enabled, use the pricing tiers
        if ($this->use_dynamic_pricing && ! empty($this->pricing_tiers)) {
            // Sort tiers by quantity in descending order to find the best match
            $sortedTiers = collect($this->pricing_tiers)->sortByDesc('quantity');

            // Find the tier that matches the quantity
            foreach ($sortedTiers as $tier) {
                if ($quantity >= $tier['quantity']) {
                    return $tier['price'];
                }
            }
        }

        // Fallback to existing wholesale logic
        if ($this->wholesale_price && $quantity >= $this->wholesale_min_quantity) {
            return $this->wholesale_price;
        }

        return $this->selling_price;
    }

    /**
     * Get all available pricing tiers for display
     */
    public function getPricingTiersForDisplay(): array
    {
        // If dynamic pricing is enabled and we have tiers, use them
        if ($this->use_dynamic_pricing && ! empty($this->pricing_tiers)) {
            return $this->pricing_tiers;
        }

        // Otherwise, create a default structure with retail and wholesale
        $tiers = [
            [
                'quantity' => 1,
                'price' => $this->selling_price,
                'label' => 'Eceran',
            ],
        ];

        // Add wholesale tier if available
        if ($this->wholesale_price && $this->wholesale_min_quantity) {
            $tiers[] = [
                'quantity' => $this->wholesale_min_quantity,
                'price' => $this->wholesale_price,
                'label' => 'Grosir',
            ];
        }

        return $tiers;
    }
}
