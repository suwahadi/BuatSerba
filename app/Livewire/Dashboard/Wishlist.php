<?php

namespace App\Livewire\Dashboard;

use App\Services\WishlistService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.dashboard')]
#[Title('Wishlist Saya - BuatSerba')]
class Wishlist extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function mount(WishlistService $service)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $service->flushPendingFromSession(auth()->user());
    }

    public function remove(int $skuId): void
    {
        app(WishlistService::class)->remove(auth()->user(), $skuId);

        $this->dispatch('notify',
            message: 'Produk dihapus dari Wishlist.',
            type: 'success'
        );

        $this->resetPage();
    }

    public function render()
    {
        $items = app(WishlistService::class)->paginate(auth()->user(), 12);

        return view('livewire.dashboard.wishlist', [
            'items' => $items,
        ]);
    }
}
