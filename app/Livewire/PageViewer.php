<?php

namespace App\Livewire;

use App\Models\Page;
use Livewire\Component;

class PageViewer extends Component
{
    public $page;

    public function mount($slug)
    {
        $this->page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.page-viewer')
            ->layout('components.layouts.guest')
            ->title($this->page->title);
    }
}
