<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Page;
use Livewire\Component;

class SlugRouter extends Component
{
    public $slug;

    public $componentName;

    public $title;

    public function mount($slug)
    {
        $this->slug = $slug;

        // Check Page
        $page = Page::where('slug', $slug)->where('is_active', true)->first();
        if ($page) {
            $this->componentName = 'page-viewer';
            $this->title = $page->title;

            return;
        }

        // Check Category
        $category = Category::where('slug', $slug)->where('is_active', true)->first();
        if ($category) {
            $this->componentName = 'category-product';
            $this->title = $category->name.' - BuatSerba';

            return;
        }

        abort(404);
    }

    public function render()
    {
        return view('livewire.slug-router')
            ->layout('components.layouts.guest')
            ->title($this->title);
    }
}
