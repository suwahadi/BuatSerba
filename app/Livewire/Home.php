<?php

namespace App\Livewire;

use App\Models\Banner;
use App\Models\Category;
use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        $banners = Banner::where('is_active', true)
            ->orderBy('sort')
            ->latest()
            ->get();

        $categories = Category::where('is_active', true)
            ->whereNotNull('image')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return view('livewire.home', [
            'banners' => $banners,
            'categories' => $categories,
        ])->layout('components.layouts.guest');
    }
}
