<?php

namespace App\Services;

use App\Models\Sku;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Pagination\LengthAwarePaginator;

class WishlistService
{
    public function add(User $user, int $skuId): Wishlist
    {
        return Wishlist::firstOrCreate([
            'user_id' => $user->id,
            'sku_id' => $skuId,
        ]);
    }

    public function remove(User $user, int $skuId): bool
    {
        return Wishlist::where('user_id', $user->id)
            ->where('sku_id', $skuId)
            ->delete() > 0;
    }

    public function toggle(User $user, int $skuId): array
    {
        $existing = Wishlist::where('user_id', $user->id)
            ->where('sku_id', $skuId)
            ->first();

        if ($existing) {
            $existing->delete();

            return [
                'active' => false,
                'message' => 'Produk dihapus dari Wishlist Anda.',
            ];
        }

        Wishlist::create([
            'user_id' => $user->id,
            'sku_id' => $skuId,
        ]);

        return [
            'active' => true,
            'message' => 'Produk sudah dimasukkan ke daftar Wishlist Anda.',
        ];
    }

    public function isWishlisted(User $user, int $skuId): bool
    {
        return Wishlist::where('user_id', $user->id)
            ->where('sku_id', $skuId)
            ->exists();
    }

    public function count(User $user): int
    {
        return Wishlist::where('user_id', $user->id)->count();
    }

    /**
     * Preload helper: returns the subset of $skuIds that user already wishlisted.
     * Used by listing pages to avoid N+1 queries per WishlistButton.
     *
     * @param  array<int>  $skuIds
     * @return array<int>
     */
    public function getWishlistedSkuIds(User $user, array $skuIds): array
    {
        if (empty($skuIds)) {
            return [];
        }

        return Wishlist::where('user_id', $user->id)
            ->whereIn('sku_id', $skuIds)
            ->pluck('sku_id')
            ->all();
    }

    public function paginate(User $user, int $perPage = 12): LengthAwarePaginator
    {
        return Wishlist::with(['sku.product.category'])
            ->where('user_id', $user->id)
            ->whereHas('sku', fn ($q) => $q->where('is_active', true))
            ->whereHas('sku.product', fn ($q) => $q->where('is_active', true))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Consume the `wishlist_pending_sku_id` flag stored before a guest's redirect to login,
     * adding it to the freshly-authenticated user's wishlist. No-op when the key is absent
     * or the SKU is no longer active.
     */
    public function flushPendingFromSession(User $user): ?Wishlist
    {
        $skuId = session()->pull('wishlist_pending_sku_id');

        if (! $skuId) {
            return null;
        }

        if (! Sku::where('id', $skuId)->where('is_active', true)->exists()) {
            return null;
        }

        return $this->add($user, (int) $skuId);
    }
}
