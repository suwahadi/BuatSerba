<?php

use App\Livewire\Home;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Livewire\Livewire;

it('renders blog mosaic heading on home page', function () {
    Livewire::test(Home::class)
        ->assertStatus(200)
        ->assertSee('Latest Update')
        ->assertSee('Blog Terbaru Kami');
});

it('shows placeholder cells when there are no published posts', function () {
    Livewire::test(Home::class)
        ->assertStatus(200)
        ->assertSee('Coming Soon')
        ->assertSee(asset('images/placeholder.jpg'));
});

it('renders published blog post titles in the mosaic', function () {
    $category = BlogCategory::create([
        'name' => 'Inspirasi',
        'slug' => 'inspirasi',
        'is_active' => true,
    ]);

    $post = BlogPost::create([
        'category_id' => $category->id,
        'title' => 'Tampil Ringan Untuk Hari-harimu',
        'slug' => 'tampil-ringan',
        'thumbnail' => null,
        'content' => '<p>body</p>',
        'is_active' => true,
        'published_at' => now()->subHour(),
    ]);

    Livewire::test(Home::class)
        ->assertStatus(200)
        ->assertSee('Tampil Ringan Untuk Hari-harimu')
        ->assertSee(route('blog.detail', $post->slug));
});

it('skips draft and future-scheduled posts', function () {
    $category = BlogCategory::create([
        'name' => 'Inspirasi',
        'slug' => 'inspirasi',
        'is_active' => true,
    ]);

    BlogPost::create([
        'category_id' => $category->id,
        'title' => 'Draft Post Should Not Render',
        'slug' => 'draft-post',
        'content' => '<p>body</p>',
        'is_active' => false,
        'published_at' => now()->subDay(),
    ]);

    BlogPost::create([
        'category_id' => $category->id,
        'title' => 'Scheduled Post Should Not Render',
        'slug' => 'scheduled-post',
        'content' => '<p>body</p>',
        'is_active' => true,
        'published_at' => now()->addDay(),
    ]);

    Livewire::test(Home::class)
        ->assertStatus(200)
        ->assertDontSee('Draft Post Should Not Render')
        ->assertDontSee('Scheduled Post Should Not Render');
});

it('falls back to placeholder image when post thumbnail is null', function () {
    $category = BlogCategory::create([
        'name' => 'Inspirasi',
        'slug' => 'inspirasi',
        'is_active' => true,
    ]);

    BlogPost::create([
        'category_id' => $category->id,
        'title' => 'No Thumbnail Post',
        'slug' => 'no-thumb',
        'thumbnail' => null,
        'content' => '<p>body</p>',
        'is_active' => true,
        'published_at' => now()->subHour(),
    ]);

    Livewire::test(Home::class)
        ->assertStatus(200)
        ->assertSee(asset('images/placeholder.jpg'));
});
