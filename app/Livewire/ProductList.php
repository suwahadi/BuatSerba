<?php

namespace App\Livewire;

use App\Models\FlashSaleItem;
use App\Models\Product;
use App\Services\WishlistService;
use Livewire\Component;

class ProductList extends Component
{
    public $perPage = 12;

    public $type = 'best-selling';

    public function loadMore()
    {
        $this->perPage += 12;
    }

    public function render()
    {
        $query = Product::with(['category', 'skus'])
            ->where('is_active', true);

        if ($this->type === 'latest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($this->type === 'random') {
            $query->inRandomOrder();
        } else {
            $query->orderBy('is_featured', 'desc')
                ->orderBy('view_count', 'desc');
        }

        $products = $query->take($this->perPage)->get();

        $totalProducts = Product::where('is_active', true)->count();

        $flashMap = FlashSaleItem::activeMapByProduct($products->pluck('id')->all());

        $wishlistedSkuIds = [];
        if (auth()->check()) {
            $skuIds = $products->flatMap(fn ($p) => $p->skus->pluck('id'))->all();
            $wishlistedSkuIds = app(WishlistService::class)
                ->getWishlistedSkuIds(auth()->user(), $skuIds);
        }

        return view('livewire.product-list', [
            'products' => $products,
            'total' => $totalProducts,
            'flashMap' => $flashMap,
            'wishlistedSkuIds' => $wishlistedSkuIds,
        ]);
    }
}
