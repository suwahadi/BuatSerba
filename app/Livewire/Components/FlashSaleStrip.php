<?php

namespace App\Livewire\Components;

use App\Models\FlashSale;
use Livewire\Component;

class FlashSaleStrip extends Component
{
    public function render()
    {
        $flashSale = FlashSale::active()
            ->with(['items' => function ($query) {
                $query->orderBy('sort')->orderBy('id')->with('sku.product');
            }])
            ->orderBy('sort')
            ->first();

        $items = $flashSale
            ? $flashSale->items->filter(fn ($item) => $item->sku && $item->sku->product)
            : collect();

        return view('livewire.components.home.flash-sale-strip', [
            'flashSale' => $flashSale,
            'items' => $items,
        ]);
    }
}
