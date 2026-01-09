<?php

namespace App\Livewire;

use App\Models\Voucher;
use Livewire\Component;

class VoucherCarousel extends Component
{
    public function render()
    {
        $vouchers = Voucher::where('is_active', true)
            ->whereNotNull('image')
            ->orderBy('sort')
            ->get();

        return view('livewire.voucher-carousel', [
            'vouchers' => $vouchers,
        ]);
    }
}
