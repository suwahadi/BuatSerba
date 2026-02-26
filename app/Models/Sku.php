<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    use HasFactory;

    protected $table = 'skus';

    protected $fillable = [
        'product_id',
        'name',
        'image',
        'sku',
        'attributes',
        'unit_cost',
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

    protected $casts = [
        'is_active' => 'boolean',
        'attributes' => 'array',
        'unit_cost' => 'decimal:2',
        'base_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'pricing_tiers' => 'array',
        'use_dynamic_pricing' => 'boolean',
    ];

    /**
     * Provide sensible model-level defaults so new SKUs created via the
     * Filament repeater will include non-nullable DB columns (e.g. weight).
     */
    protected $attributes = [
        'weight' => 0,
        'unit_cost' => 0,
        'base_price' => 0,
        'selling_price' => 0,
        'stock_quantity' => 0,
        'is_active' => true,
    ];

    /**
     * Ensure attributes JSON always contains sensible defaults for the form.
     * This avoids empty repeater fields for legacy/default SKUs that have
     * null attributes in the database.
     */
    public function getAttributesAttribute($value): array
    {
        $raw = $this->getRawOriginal('attributes');

        $attrs = [];
        if ($raw !== null) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $attrs = $decoded;
            }
        }

        if (empty($attrs['name'])) {
            $attrs['name'] = '';
        }

        if (! array_key_exists('image', $attrs)) {
            $attrs['image'] = null;
        }

        return $attrs;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Virtual `name` attribute mapped into the JSON `attributes` column.
     */
    public function getNameAttribute(): ?string
    {
        $raw = $this->getRawOriginal('attributes');
        if ($raw === null) {
            return null;
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) && isset($decoded['name']) ? $decoded['name'] : null;
    }

    public function setNameAttribute($value): void
    {
        $raw = $this->getRawOriginal('attributes');
        $attrs = [];
        if ($raw !== null) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $attrs = $decoded;
            }
        }

        $attrs['name'] = $value;

        $this->setAttribute('attributes', $attrs);
    }

    /**
     * Virtual `image` attribute mapped into the JSON `attributes` column.
     */
    public function getImageAttribute(): ?string
    {
        $raw = $this->getRawOriginal('attributes');
        if ($raw === null) {
            return null;
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) && isset($decoded['image']) ? $decoded['image'] : null;
    }

    public function setImageAttribute($value): void
    {
        $raw = $this->getRawOriginal('attributes');
        $attrs = [];
        if ($raw !== null) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $attrs = $decoded;
            }
        }

        $attrs['image'] = $value;
        $this->setAttribute('attributes', $attrs);
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
        if ($this->use_dynamic_pricing && ! empty($this->pricing_tiers)) {
            $sortedTiers = collect($this->pricing_tiers)->sortByDesc('quantity');

            foreach ($sortedTiers as $tier) {
                if ($quantity >= $tier['quantity']) {
                    return $tier['price'];
                }
            }
        }

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
        if ($this->use_dynamic_pricing && ! empty($this->pricing_tiers)) {
            return $this->pricing_tiers;
        }

        $tiers = [
            [
                'quantity' => 1,
                'price' => $this->selling_price,
                'label' => 'Eceran',
            ],
        ];

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
