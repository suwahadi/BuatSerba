<?php

namespace App\Livewire\Wallet;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    #[Computed]
    public function ledgers()
    {
        return auth()->user()->wallet->ledgers()
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function totalCashback()
    {
        return auth()->user()->wallet->ledgers()
            ->where('description', 'like', 'Premium cashback%')
            ->sum('amount');
    }

    public function render()
    {
        return view('livewire.wallet.index');
    }
}