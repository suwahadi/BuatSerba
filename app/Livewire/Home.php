<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        // Get 6 active categories with images
        $categories = Category::where('is_active', true)
            ->whereNotNull('image')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        // Get 36 best-selling products (using view_count and is_featured as proxy for best-selling)
        $bestSellingProducts = Product::with(['category', 'skus'])
            ->where('is_active', true)
            ->orderBy('is_featured', 'desc')
            ->orderBy('view_count', 'desc')
            ->limit(36)
            ->get();

        return view('livewire.home', [
            'categories' => $categories,
            'bestSellingProducts' => $bestSellingProducts,
        ])->layout('components.layouts.guest');
    }
}
