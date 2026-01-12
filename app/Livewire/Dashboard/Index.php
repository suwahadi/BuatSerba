<?php

namespace App\Livewire\Dashboard;

use App\Models\Order;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.dashboard')]
#[Title('Dashboard - BuatSerba')]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $category = '';

    public $dateFilter = '';

    public $statusFilter = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'category', 'dateFilter', 'statusFilter']);
        $this->resetPage();
    }

    #[Computed]
    public function orders()
    {
        return Order::with(['items.product', 'reviews'])
            ->where('user_id', auth()->id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('order_number', 'like', '%'.$this->search.'%')
                        ->orWhere('customer_name', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                switch ($this->statusFilter) {
                    case 'pending':
                        $query->where('payment_status', 'pending');
                        break;
                    case 'completed':
                        $query->where('status', 'completed');
                        break;
                    case 'failed':
                        $query->whereIn('status', ['cancelled', 'payment_failed']);
                        break;
                }
            })
            ->when($this->dateFilter, function ($query) {
                $query->whereDate('created_at', $this->dateFilter);
            })
            ->latest()
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.dashboard.index');
    }
}
