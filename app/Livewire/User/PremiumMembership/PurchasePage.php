<?php

namespace App\Livewire\User\PremiumMembership;

use App\Models\PremiumMembership;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

#[Title('Beli Premium Membership')]
class PurchasePage extends Component
{
    public $price = 100000;
    public $activeMembership = null;
    public $daysRemaining = null;
    public $pendingMembership = null;

    // Modal states
    public $showPurchaseModal = false;
    public $showUploadModal = false;
    public $showSuccessModal = false;

    // Upload form
    public $membershipId = null;
    public $proofFile = null;
    public $uploadLoading = false;
    public $uploadError = null;
    public $termsAccepted = false;

    public function mount()
    {
        $user = Auth::user();
        $this->activeMembership = $user->activePremiumMembership()->first();
        
        if ($this->activeMembership) {
            $this->daysRemaining = $this->activeMembership->daysRemaining();
        }

        // Check if user has pending membership
        $this->pendingMembership = $user->premiumMemberships()
            ->where('status', 'pending')
            ->first();
    }

    public function openPurchaseModal()
    {
        $this->showPurchaseModal = true;
    }

    public function closePurchaseModal()
    {
        $this->showPurchaseModal = false;
    }

    public function purchaseMembership()
    {
        if (!$this->termsAccepted) {
            $this->dispatch('notify', type: 'error', message: 'Silakan setujui syarat dan ketentuan terlebih dahulu.');
            return;
        }

        $user = Auth::user();

        // Check if user already has pending or active membership
        $existingPending = $user->premiumMemberships()
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            $this->dispatch('notify', type: 'warning', message: 'Anda sudah memiliki pembelian premium yang menunggu verifikasi. Silakan upload bukti transfer terlebih dahulu.');
            return;
        }

        try {
            // Create new pending membership
            $membership = $user->premiumMemberships()->create([
                'price' => $this->price,
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
            ]);

            $this->membershipId = $membership->id;
            $this->pendingMembership = $membership;
            
            $this->showPurchaseModal = false;
            $this->showUploadModal = true;
            $this->termsAccepted = false;

            $this->dispatch('notify', type: 'success', message: 'Pembelian premium dibuat. Silakan upload bukti transfer.');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Error: ' . $e->getMessage());
        }
    }

    public function uploadProof()
    {
        $this->validate([
            'proofFile' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'proofFile.required' => 'Silakan pilih file bukti transfer.',
            'proofFile.image' => 'File harus berupa gambar.',
            'proofFile.mimes' => 'Format file harus JPG atau PNG.',
            'proofFile.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        if (!$this->membershipId) {
            $this->dispatch('notify', type: 'error', message: 'Membership ID tidak ditemukan.');
            return;
        }

        try {
            $this->uploadLoading = true;
            $this->uploadError = null;

            $membership = PremiumMembership::findOrFail($this->membershipId);
            $user = Auth::user();

            // Ensure user owns this membership
            if ($membership->user_id !== $user->id) {
                throw new \Exception('Unauthorized');
            }

            // Only pending memberships can upload proof
            if ($membership->status !== 'pending') {
                throw new \Exception('Hanya pembelian yang pending yang bisa upload bukti transfer.');
            }

            // Delete old proof if exists
            if ($membership->payment_proof_path) {
                Storage::disk('public')->delete($membership->payment_proof_path);
            }

            // Store new proof
            $path = $this->proofFile->store('premium-proof', 'public');
            $membership->update(['payment_proof_path' => $path]);

            $this->showUploadModal = false;
            $this->showSuccessModal = true;
            $this->proofFile = null;

            // Reset after 2 seconds
            $this->dispatch('nextStep');
        } catch (\Exception $e) {
            $this->uploadError = 'Gagal upload bukti: ' . $e->getMessage();
            $this->dispatch('notify', type: 'error', message: $this->uploadError);
        } finally {
            $this->uploadLoading = false;
        }
    }

    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->proofFile = null;
        $this->uploadError = null;
    }

    public function goToMemberships()
    {
        return redirect()->route('premium.memberships');
    }

    public function closeSuccessModal()
    {
        $this->showSuccessModal = false;
        $this->goToMemberships();
    }

    public function renew()
    {
        if (!$this->activeMembership) {
            $this->dispatch('notify', type: 'error', message: 'Anda tidak memiliki membership premium aktif untuk diperpanjang.');
            return;
        }

        $user = Auth::user();

        // Check if already has pending renewal
        $pendingRenewal = $user->premiumMemberships()
            ->where('status', 'pending')
            ->where('created_at', '>', now()->subDay())
            ->first();

        if ($pendingRenewal) {
            $this->dispatch('notify', type: 'warning', message: 'Anda sudah memiliki perpanjangan membership yang menunggu verifikasi.');
            return;
        }

        try {
            // Create new membership for renewal
            $newMembership = $user->premiumMemberships()->create([
                'price' => $this->price,
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
            ]);

            $this->membershipId = $newMembership->id;
            $this->pendingMembership = $newMembership;
            $this->showUploadModal = true;

            $this->dispatch('notify', type: 'success', message: 'Perpanjangan membership dibuat. Silakan upload bukti transfer.');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user.premium-membership.purchase-page');
    }
}
