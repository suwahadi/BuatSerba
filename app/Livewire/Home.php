<?php

namespace App\Livewire;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Page;
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

        $aboutPage = Page::where('slug', 'about')->where('is_active', true)->first();
        $aboutSummary = '';

        if ($aboutPage) {
            preg_match_all('/<p[^>]*>.*?<\/p>/si', $aboutPage->content, $matches);
            if (!empty($matches[0])) {
                $aboutSummary = implode('', array_slice($matches[0], 0, 3));
            } else {
                $aboutSummary = \Illuminate\Support\Str::limit(strip_tags($aboutPage->content), 500);
            }
        }

        return view('livewire.home', [
            'banners' => $banners,
            'categories' => $categories,
            'aboutSummary' => $aboutSummary,
        ])->layout('components.layouts.guest');
    }
}
