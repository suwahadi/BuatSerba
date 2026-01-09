<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryProduct extends Component
{
    use WithPagination;

    public $category;

    public $slug;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function render()
    {
        $products = Product::where('category_id', $this->category->id)
            ->where('is_active', true)
            ->with(['skus' => function ($query) {
                $query->where('is_active', true);
            }, 'category'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->paginate(20);

        return view('livewire.category-product', [
            'products' => $products,
        ])
            ->layout('components.layouts.guest')
            ->title($this->category->name.' - BuatSerba');
    }
}
