<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Product;
use App\Models\Sku;
use App\Services\WishlistService;
use Livewire\Component;
use Livewire\WithPagination;

class Catalog extends Component
{
    use WithPagination;

    public $search = '';

    public $selectedCategories = [];

    public $selectedBrands = [];

    public $selectedRatings = [];

    public $minPrice = 0;

    public $maxPrice = 50000000;

    public $availability = [];

    public $sortBy = 'popularity';

    public $viewMode = 'grid';

    public $perPage = 12;

    public $showMobileFilters = false;

    public $flashOnly = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategories' => ['except' => []],
        'selectedBrands' => ['except' => []],
        'sortBy' => ['except' => 'popularity'],
        'flashOnly' => ['except' => false, 'as' => 'flash'],
    ];

    public function mount()
    {
        // Accept boolean-ish values from query string (?flash=1, ?flash=true)
        $this->flashOnly = filter_var($this->flashOnly, FILTER_VALIDATE_BOOLEAN);
    }

    public function clearFlashFilter(): void
    {
        $this->flashOnly = false;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategories()
    {
        $this->resetPage();
    }

    public function updatedMinPrice()
    {
        $this->resetPage();
    }

    public function updatedMaxPrice()
    {
        $this->resetPage();
    }

    public function toggleCategory($categoryId)
    {
        if (in_array($categoryId, $this->selectedCategories)) {
            $this->selectedCategories = array_diff($this->selectedCategories, [$categoryId]);
        } else {
            $this->selectedCategories[] = $categoryId;
        }
        $this->resetPage();
    }

    public function toggleBrand($brand)
    {
        if (in_array($brand, $this->selectedBrands)) {
            $this->selectedBrands = array_diff($this->selectedBrands, [$brand]);
        } else {
            $this->selectedBrands[] = $brand;
        }
        $this->resetPage();
    }

    public function toggleRating($rating)
    {
        if (in_array($rating, $this->selectedRatings)) {
            $this->selectedRatings = array_diff($this->selectedRatings, [$rating]);
        } else {
            $this->selectedRatings[] = $rating;
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['selectedCategories', 'selectedBrands', 'selectedRatings', 'availability', 'search']);
        $this->minPrice = 0;
        $this->maxPrice = 50000000;
        $this->resetPage();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function toggleMobileFilters()
    {
        $this->showMobileFilters = !$this->showMobileFilters;
        $this->dispatch('filter-toggled', show: $this->showMobileFilters);
    }

    public function closeMobileFilters()
    {
        $this->showMobileFilters = false;
        $this->dispatch('filter-toggled', show: false);
    }

    public function applyFilters()
    {
        $this->showMobileFilters = false;
        $this->dispatch('filter-toggled', show: false);
        $this->resetPage();
    }

    public function getProducts()
    {
        $query = Product::with(['category', 'skus'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('products.is_active', true);

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('products.name', 'like', '%'.$this->search.'%')
                    ->orWhere('products.description', 'like', '%'.$this->search.'%')
                    ->orWhereHas('category', function ($catQuery) {
                        $catQuery->where('categories.name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        // Category filter
        if (! empty($this->selectedCategories)) {
            $query->whereIn('products.category_id', $this->selectedCategories);
        }

        // Flash sale filter — restrict to products that have an item in the active sale
        if ($this->flashOnly) {
            $activeSale = FlashSale::active()->orderBy('sort')->orderBy('id')->first();
            if ($activeSale) {
                $query->whereIn('products.id', function ($sub) use ($activeSale) {
                    $sub->select('skus.product_id')
                        ->from('flash_sale_items')
                        ->join('skus', 'skus.id', '=', 'flash_sale_items.sku_id')
                        ->where('flash_sale_items.flash_sale_id', $activeSale->id);
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // Price filter (via SKU)
        if ($this->minPrice > 0 || $this->maxPrice < 50000000) {
            $query->whereHas('skus', function ($skuQuery) {
                $skuQuery->whereBetween('skus.selling_price', [$this->minPrice, $this->maxPrice])
                    ->where('skus.is_active', true);
            });
        }

        // Sort
        switch ($this->sortBy) {
            case 'newest':
                $query->orderBy('products.created_at', 'desc');
                break;
            case 'price-low':
                $query->orderBy(
                    \App\Models\Sku::select('selling_price')
                        ->whereColumn('product_id', 'products.id')
                        ->where('is_active', true)
                        ->orderBy('selling_price', 'asc')
                        ->limit(1),
                    'asc'
                );
                break;
            case 'price-high':
                $query->orderBy(
                    \App\Models\Sku::select('selling_price')
                        ->whereColumn('product_id', 'products.id')
                        ->where('is_active', true)
                        ->orderBy('selling_price', 'desc')
                        ->limit(1),
                    'desc'
                );
                break;
            case 'random':
                $query->inRandomOrder();
                break;
            case 'rating':
                $query->orderByDesc('reviews_avg_rating');
                break;
            default:
                $query->orderBy('products.is_featured', 'desc')
                    ->orderBy('products.view_count', 'desc');
        }

        return $query->paginate($this->perPage);
    }

    public function updatedFlashOnly()
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = $this->getProducts();
        $categories = Category::withCount('products')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $flashMap = FlashSaleItem::activeMapByProduct($products->pluck('id')->all());

        $wishlistedSkuIds = [];
        if (auth()->check()) {
            $skuIds = $products->flatMap(fn ($p) => $p->skus->pluck('id'))->all();
            $wishlistedSkuIds = app(WishlistService::class)
                ->getWishlistedSkuIds(auth()->user(), $skuIds);
        }

        return view('livewire.catalog', [
            'products' => $products,
            'categories' => $categories,
            'flashMap' => $flashMap,
            'wishlistedSkuIds' => $wishlistedSkuIds,
        ])->layout('components.layouts.guest');
    }
}
