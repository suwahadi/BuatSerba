<?php

namespace App\Livewire\Dashboard;

use App\Models\MemberBalanceLedger;
use App\Services\MemberWalletService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.dashboard')]
#[Title('Saldo Saya - BuatSerba')]
class MemberBalance extends Component
{
    use WithPagination;

    public $balance;
    public $lockedBalance;
    public $availableBalance;
    public $initialBalance;

    public $search = '';
    public $typeFilter = 'all';
    public $dateFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => 'all'],
    ];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $this->loadBalance();
    }

    public function loadBalance()
    {
        $memberWalletService = new MemberWalletService();
        $wallet = $memberWalletService->getOrCreateWalletById(auth()->id());

        $this->balance = $wallet->balance;
        $this->lockedBalance = $wallet->locked_balance;
        $this->availableBalance = $wallet->available_balance;
        $this->initialBalance = $wallet->initial_balance;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'typeFilter', 'dateFilter']);
        $this->resetPage();
    }

    #[Computed]
    public function transactions()
    {
        return auth()->user()
            ->balanceLedgers()
            ->with('order', 'voucher')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%'.$this->search.'%')
                        ->orWhere('reference_code', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->typeFilter !== 'all', function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->dateFilter, function ($query) {
                $query->whereDate('created_at', $this->dateFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getTypeLabel($type)
    {
        return match($type) {
            'credit' => 'Masuk',
            'debit' => 'Keluar',
            default => ucfirst($type),
        };
    }

    public function getTypeColor($type)
    {
        return match($type) {
            'credit' => 'text-green-600',
            'debit' => 'text-red-600',
            default => 'text-gray-600',
        };
    }

    public function getReferenceLabel($transaction)
    {
        if (!$transaction->source_type || !$transaction->source_id) {
            return '-';
        }

        switch($transaction->source_type) {
            case 'order_payment':
                return 'Order #' . ($transaction->order->order_number ?? $transaction->source_id);
            case 'order_cancellation_refund':
                return 'Refund Order #' . ($transaction->order->order_number ?? $transaction->source_id);
            case 'voucher_cashback':
                return 'Cashback #' . ($transaction->voucher->voucher_code ?? $transaction->source_id);
            case 'admin_credit':
                return 'Kredit Admin #' . $transaction->source_id;
            case 'admin_debit':
                return 'Debit Admin #' . $transaction->source_id;
            default:
                return ucfirst(str_replace('_', ' ', $transaction->source_type)) . ' #' . $transaction->source_id;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.member-balance');
    }
}
