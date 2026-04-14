<?php

namespace App\Livewire;

use App\Models\BlogPost;
use Livewire\Component;

class BlogDetail extends Component
{
    public $slug;

    public function mount($slug)
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $post = BlogPost::with('category')
            ->where('slug', $this->slug)
            ->where('is_active', true)
            ->firstOrFail();

        $post->incrementViewCount();

        $relatedPosts = BlogPost::with('category')
            ->where('is_active', true)
            ->where('id', '!=', $post->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        $metaSeo = is_array($post->meta_seo) && !empty($post->meta_seo) ? $post->meta_seo[0] : null;

        $title = $metaSeo['title'] ?? $post->title;
        $description = $metaSeo['description'] ?? \Illuminate\Support\Str::limit(strip_tags($post->content), 160);
        $ogTitle = $metaSeo['og_title'] ?? $title;
        $ogDescription = $metaSeo['og_description'] ?? $description;
        $ogImage = $metaSeo['og_image'] ?? ($post->thumbnail ? asset('storage/' . $post->thumbnail) : null);
        $twitterCard = $metaSeo['twitter_card'] ?? 'summary_large_image';

        return view('livewire.blog-detail', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ])->layout('components.layouts.guest', [
            'title' => $title,
            'meta' => [
                'description' => $description,
                'og:type' => 'article',
                'og:title' => $ogTitle,
                'og:description' => $ogDescription,
                'og:image' => $ogImage,
                'og:url' => url()->current(),
                'twitter:card' => $twitterCard,
                'twitter:image' => $ogImage,
            ],
        ]);
    }
}
