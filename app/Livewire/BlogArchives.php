<?php

namespace App\Livewire;

use App\Models\BlogPost;
use Livewire\Component;

class BlogArchives extends Component
{
    public $perPage = 12;

    public function loadMore()
    {
        $this->perPage += 12;
    }

    public function render()
    {
        $posts = BlogPost::with('category')
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->take($this->perPage)
            ->get();

        $totalPosts = BlogPost::where('is_active', true)->count();
        $hasMore = $this->perPage < $totalPosts;

        return view('livewire.blog-archives', [
            'posts' => $posts,
            'hasMore' => $hasMore,
        ])->layout('components.layouts.guest');
    }
}
