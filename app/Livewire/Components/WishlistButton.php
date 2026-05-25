<?php

namespace App\Livewire\Components;

use App\Services\WishlistService;
use Livewire\Component;

class WishlistButton extends Component
{
    public ?int $skuId = null;

    public string $variant = 'card';

    public bool $isActive = false;

    public function mount(?int $skuId = null, string $variant = 'card', ?bool $isActiveInitial = null): void
    {
        $this->skuId = $skuId;
        $this->variant = $variant;

        if (! $this->skuId) {
            return;
        }

        if ($isActiveInitial !== null) {
            $this->isActive = $isActiveInitial;

            return;
        }

        if (auth()->check()) {
            $this->isActive = app(WishlistService::class)
                ->isWishlisted(auth()->user(), $this->skuId);
        }
    }

    public function toggle()
    {
        if (! $this->skuId) {
            return null;
        }

        if (auth()->guest()) {
            session(['wishlist_pending_sku_id' => $this->skuId]);

            return redirect()->route('login');
        }

        $result = app(WishlistService::class)->toggle(auth()->user(), $this->skuId);

        $this->isActive = $result['active'];

        $this->dispatch('wishlist-added',
            message: $result['message'],
            active: $result['active'],
            manageUrl: route('user.wishlist'),
        );

        return null;
    }

    public function render()
    {
        return view('livewire.components.wishlist-button');
    }
}
