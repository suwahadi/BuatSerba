<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class FlashSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'banner_image',
        'starts_at',
        'ends_at',
        'is_active',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
            'sort' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (FlashSale $flashSale): void {
            if (empty($flashSale->slug) && ! empty($flashSale->name)) {
                $flashSale->slug = static::generateUniqueSlug($flashSale->name, $flashSale->id);
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(FlashSaleItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query->where('is_active', true)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now);
    }

    public function isLive(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = Carbon::now();

        return $this->starts_at <= $now && $this->ends_at >= $now;
    }

    public function remainingSeconds(): int
    {
        if (! $this->ends_at) {
            return 0;
        }

        return max(0, Carbon::now()->diffInSeconds($this->ends_at, false));
    }

    protected static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'flash-sale';
        }

        $slug = $base;
        $suffix = 1;

        while (
            static::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $suffix++;
            $slug = "{$base}-{$suffix}";
        }

        return $slug;
    }
}
