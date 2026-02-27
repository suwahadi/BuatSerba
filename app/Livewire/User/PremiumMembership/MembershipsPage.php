<?php

namespace App\Livewire\User\PremiumMembership;

use App\Models\PremiumMembership;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[Title('Membership Premium Saya')]
class MembershipsPage extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $statusFilter = 'all';
    public $dateFilter = '';

    // Modal states
    public $showDetailModal = false;
    public $showDeleteModal = false;
    public $selectedMembership = null;

    // Counts
    public $activeMembershipCount = 0;
    public $pendingMembershipCount = 0;
    public $expiredMembershipCount = 0;

    public function mount()
    {
        $this->updateCounts();
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'statusFilter', 'dateFilter'])) {
            $this->resetPage();
        }
    }

    private function updateCounts()
    {
        $user = Auth::user();
        $this->activeMembershipCount = $user->premiumMemberships()->where('status', 'active')->count();
        $this->pendingMembershipCount = $user->premiumMemberships()->where('status', 'pending')->count();
        $this->expiredMembershipCount = $user->premiumMemberships()->where('status', 'expired')->count();
    }

    public function getMembershipsProperty()
    {
        $user = Auth::user();
        $query = $user->premiumMemberships();

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('payment_method', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Date filter
        if ($this->dateFilter) {
            $query->whereDate('created_at', $this->dateFilter);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getStatusColorProperty()
    {
        return [
            'pending' => 'warning',
            'active' => 'success',
            'expired' => 'danger',
            'cancelled' => 'gray',
        ];
    }

    public function getStatusLabelProperty()
    {
        return [
            'pending' => 'Menunggu Verifikasi',
            'active' => 'Aktif',
            'expired' => 'Kedaluwarsa',
            'cancelled' => 'Dibatalkan',
        ];
    }

    public function viewDetail(PremiumMembership $membership)
    {
        $user = Auth::user();
        if ($membership->user_id !== $user->id) {
            $this->dispatch('notify', type: 'error', message: 'Anda tidak memiliki izin untuk aksi ini.');
            return;
        }

        $this->selectedMembership = $membership;
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedMembership = null;
    }

    public function openDeleteModal(PremiumMembership $membership)
    {
        $user = Auth::user();
        if ($membership->user_id !== $user->id) {
            $this->dispatch('notify', type: 'error', message: 'Anda tidak memiliki izin untuk aksi ini.');
            return;
        }

        if (!in_array($membership->status, ['pending', 'expired'])) {
            $this->dispatch('notify', type: 'error', message: 'Hanya membership pending dan expired yang bisa dihapus.');
            return;
        }

        $this->selectedMembership = $membership;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedMembership = null;
    }

    public function deleteMembership()
    {
        if (!$this->selectedMembership) {
            return;
        }

        try {
            $membership = PremiumMembership::findOrFail($this->selectedMembership->id);
            $user = Auth::user();

            if ($membership->user_id !== $user->id) {
                throw new \Exception('Unauthorized');
            }

            $membership->delete();
            $this->showDeleteModal = false;
            $this->selectedMembership = null;
            $this->updateCounts();

            $this->dispatch('notify', type: 'success', message: 'Membership berhasil dihapus.');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Error: ' . $e->getMessage());
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->dateFilter = '';
        $this->resetPage();
    }

    public function getStatusBadgeColorClass($status)
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'active' => 'bg-green-100 text-green-800',
            'expired' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getDaysRemaining($membership)
    {
        if ($membership->status !== 'active') {
            return null;
        }
        
        return $membership->daysRemaining();
    }

    public function render()
    {
        return view('livewire.user.premium-membership.memberships-page', [
            'memberships' => $this->getMembershipsProperty(),
            'activeMembershipCount' => $this->activeMembershipCount,
            'pendingMembershipCount' => $this->pendingMembershipCount,
            'expiredMembershipCount' => $this->expiredMembershipCount,
        ]);
    }
}
