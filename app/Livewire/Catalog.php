<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sku;
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

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategories' => ['except' => []],
        'selectedBrands' => ['except' => []],
        'sortBy' => ['except' => 'popularity'],
    ];

    public function mount()
    {
        // Initialize default values
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

    public function getProducts()
    {
        $query = Product::with(['category', 'skus'])
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
                $query->join('skus', 'products.id', '=', 'skus.product_id')
                    ->where('skus.is_active', true)
                    ->select('products.*', \DB::raw('MIN(skus.selling_price) as min_price'))
                    ->groupBy('products.id')
                    ->orderBy('min_price', 'asc');
                break;
            case 'price-high':
                $query->join('skus', 'products.id', '=', 'skus.product_id')
                    ->where('skus.is_active', true)
                    ->select('products.*', \DB::raw('MAX(skus.selling_price) as max_price'))
                    ->groupBy('products.id')
                    ->orderBy('max_price', 'desc');
                break;
            case 'rating':
                $query->orderBy('products.view_count', 'desc'); // Placeholder, should use actual rating
                break;
            default:
                $query->orderBy('products.is_featured', 'desc')
                    ->orderBy('products.view_count', 'desc');
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        $products = $this->getProducts();
        $categories = Category::withCount('products')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('livewire.catalog', [
            'products' => $products,
            'categories' => $categories,
        ])->layout('components.layouts.guest');
    }
}
