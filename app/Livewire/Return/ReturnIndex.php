<?php

namespace App\Livewire\Return;

use App\Models\ReturnRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ReturnIndex extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! Auth::check()) {
            redirect()->route('login');
        }
    }

    public function render(): View
    {
        $returnRequests = ReturnRequest::where('user_id', Auth::id())
            ->with('items.orderItem')
            ->latest()
            ->paginate(10);

        return view('livewire.dashboard.return-index', [
            'returnRequests' => $returnRequests,
        ])->layout('components.layouts.dashboard');
    }
}
