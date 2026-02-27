<?php

namespace App\Livewire\Dashboard;

use App\Models\PremiumMembership;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.dashboard')]
#[Title('Membership Saya - BuatSerba')]
class PremiumMemberships extends Component
{
    use WithPagination;

    public $activeMembership;
    public $statusFilter = 'all';
    public $search = '';
    public $showDetailModal = false;
    public $selectedMembership;

    protected $queryString = [
        'statusFilter' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['refreshMemberships' => 'loadMemberships'];

    public function mount()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $this->loadMemberships();
    }

    public function loadMemberships()
    {
        $user = auth()->user();
        $this->activeMembership = $user->activePremiumMembership()->first();
    }

    #[\Livewire\Attributes\Computed]
    public function memberships()
    {
        $user = auth()->user();
        $query = $user->premiumMemberships();

        // Filter by status
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Filter by search
        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getStatusColor($status)
    {
        return match($status) {
            'pending' => 'text-yellow-600',
            'active' => 'text-green-600',
            'expired' => 'text-red-600',
            'cancelled' => 'text-gray-600',
            default => 'text-gray-600',
        };
    }

    public function getStatusBadgeColor($status)
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'active' => 'bg-green-100 text-green-800',
            'expired' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabel($status)
    {
        return match($status) {
            'pending' => 'Menunggu Verifikasi',
            'active' => 'Aktif',
            'expired' => 'Kedaluwarsa',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown',
        };
    }

    public function showDetail(PremiumMembership $membership)
    {
        // Ensure user owns this membership
        if ($membership->user_id !== auth()->id()) {
            $this->dispatch('error', message: 'Unauthorized');
            return;
        }

        $this->selectedMembership = $membership;
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedMembership = null;
    }

    public function resetFilters()
    {
        $this->statusFilter = 'all';
        $this->search = '';
    }

    public function deleteMembership(PremiumMembership $membership)
    {
        // Only allow deletion for pending/expired
        if (!in_array($membership->status, ['pending', 'expired', 'cancelled'])) {
            $this->dispatch('error', message: 'Anda hanya bisa menghapus membership yang pending atau kedaluwarsa.');
            return;
        }

        // Ensure user owns this membership
        if ($membership->user_id !== auth()->id()) {
            $this->dispatch('error', message: 'Unauthorized');
            return;
        }

        // Delete associated file if exists
        if ($membership->payment_proof_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($membership->payment_proof_path);
        }

        $membership->delete();
        $this->loadMemberships();
        $this->dispatch('success', message: 'Membership berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.dashboard.premium-memberships', [
            'memberships' => $this->memberships,
        ]);
    }
}
