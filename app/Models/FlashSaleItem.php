<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'flash_sale_id',
        'sku_id',
        'flash_price',
        'original_price_snapshot',
        'stock_limit',
        'sold_count',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'flash_price' => 'decimal:2',
            'original_price_snapshot' => 'decimal:2',
            'stock_limit' => 'integer',
            'sold_count' => 'integer',
            'sort' => 'integer',
        ];
    }

    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function sku(): BelongsTo
    {
        return $this->belongsTo(Sku::class);
    }

    public function scopeForSku(Builder $query, int $skuId): Builder
    {
        return $query->where('sku_id', $skuId);
    }

    public function scopeWithActiveSale(Builder $query): Builder
    {
        return $query->whereHas('flashSale', fn (Builder $q) => $q->active());
    }

    public function getRemainingStockAttribute(): int
    {
        return max(0, (int) $this->stock_limit - (int) $this->sold_count);
    }

    public function getIsSoldOutAttribute(): bool
    {
        return $this->remaining_stock <= 0;
    }

    public function getDiscountPercentageAttribute(): int
    {
        $original = (float) $this->original_price_snapshot;
        $price = (float) $this->flash_price;

        if ($original <= 0 || $price >= $original) {
            return 0;
        }

        return (int) round((1 - $price / $original) * 100);
    }

    /**
     * Build a map of [product_id => FlashSaleItem] for the currently active
     * flash sale (lowest sort). Returns empty collection when no sale is live.
     * Used by Catalog/ProductList to decorate product cards.
     */
    public static function activeMapByProduct(?array $productIds = null): \Illuminate\Support\Collection
    {
        $sale = FlashSale::active()->orderBy('sort')->orderBy('id')->first();
        if (! $sale) {
            return collect();
        }

        $query = static::where('flash_sale_id', $sale->id)
            ->with('sku:id,product_id,selling_price');

        if (! empty($productIds)) {
            $query->whereHas('sku', fn ($q) => $q->whereIn('product_id', $productIds));
        }

        return $query->get()
            ->filter(fn (FlashSaleItem $item) => $item->sku !== null)
            ->groupBy(fn (FlashSaleItem $item) => (int) $item->sku->product_id)
            ->map(fn ($items) => $items->sortBy('sort')->first());
    }

    /**
     * Resolve the flash sale entry for a specific SKU in the currently
     * active sale. Returns null when SKU isn't in the active sale.
     */
    public static function activeForSku(int $skuId): ?self
    {
        $sale = FlashSale::active()->orderBy('sort')->orderBy('id')->first();
        if (! $sale) {
            return null;
        }

        return static::where('flash_sale_id', $sale->id)
            ->where('sku_id', $skuId)
            ->first();
    }
}
