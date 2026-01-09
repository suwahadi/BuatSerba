<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductList extends Component
{
    public $perPage = 6;

    public $type = 'best-selling'; // 'best-selling' or 'latest'

    public function loadMore()
    {
        $this->perPage += 6;
    }

    public function render()
    {
        $query = Product::with(['category', 'skus'])
            ->where('is_active', true);

        if ($this->type === 'latest') {
            $query->orderBy('created_at', 'desc');
        } else {
            // Default best-selling
            $query->orderBy('is_featured', 'desc')
                ->orderBy('view_count', 'desc');
        }

        $products = $query->take($this->perPage)->get();

        $totalProducts = Product::where('is_active', true)->count();

        return view('livewire.product-list', [
            'products' => $products,
            'total' => $totalProducts,
        ]);
    }
}
