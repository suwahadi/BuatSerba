<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;

class CategoryCarousel extends Component
{
    public function render()
    {
        $categories = Category::where('is_active', true)
            ->whereNotNull('image')
            ->orderBy('sort_order')
            ->get();

        return view('livewire.category-carousel', [
            'categories' => $categories,
        ]);
    }
}
