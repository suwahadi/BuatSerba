<?php

namespace App\Livewire\Dashboard;

use App\Models\PremiumMembership;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.dashboard')]
#[Title('Beli Premium Membership - BuatSerba')]
class PremiumMembershipPurchase extends Component
{
    use WithFileUploads;
    public $activeMembership;
    public $pendingMembership;
    public $daysRemaining = 0;
    public $showPurchaseModal = false;
    public $showUploadModal = false;
    public $currentMembershipId;
    
    // Upload file
    public $uploadedFile;
    public $isUploading = false;
    
    // Pricing info
    public const MEMBERSHIP_PRICE = 100000;
    public const MEMBERSHIP_DURATION_DAYS = 365;

    public function mount()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $this->loadMembershipData();
    }

    public function loadMembershipData()
    {
        $user = auth()->user();
        
        // Get active membership
        $this->activeMembership = $user->activePremiumMembership()->first();
        
        // Get pending membership (most recent)
        $this->pendingMembership = $user->premiumMemberships()
            ->where('status', 'pending')
            ->latest()
            ->first();
        
        // Calculate days remaining
        if ($this->activeMembership) {
            $this->daysRemaining = $this->activeMembership->daysRemaining() ?? 0;
        }
    }

    public function purchasePremium()
    {
        $user = auth()->user();

        // Check if already has pending
        $existingPending = $user->premiumMemberships()
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            $this->dispatch('error', message: 'Anda sudah memiliki pembelian premium yang menunggu verifikasi.');
            return;
        }

        // Create pending membership
        $membership = $user->premiumMemberships()->create([
            'price' => self::MEMBERSHIP_PRICE,
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
        ]);

        $this->currentMembershipId = $membership->id;
        $this->showPurchaseModal = false;
        $this->showUploadModal = true;
        $this->loadMembershipData();
        
        $this->dispatch('success', message: 'Pembelian premium dibuat. Silakan upload bukti transfer.');
    }

    public function saveUploadedFile()
    {
        $this->validate([
            'uploadedFile' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'uploadedFile.required' => 'Silakan pilih file bukti transfer.',
            'uploadedFile.image' => 'File harus berupa gambar.',
            'uploadedFile.mimes' => 'Format file harus JPG atau PNG.',
            'uploadedFile.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        if (!$this->currentMembershipId) {
            $this->dispatch('error', message: 'Membership tidak ditemukan.');
            return;
        }

        $this->isUploading = true;

        try {
            $membership = PremiumMembership::findOrFail($this->currentMembershipId);

            // Ensure user owns this membership
            if ($membership->user_id !== auth()->id()) {
                $this->dispatch('error', message: 'Unauthorized');
                $this->isUploading = false;
                return;
            }

            // Store file
            $path = $this->uploadedFile->store('premium-proof', 'public');

            // Update membership
            $membership->update(['payment_proof_path' => $path]);

            $this->showUploadModal = false;
            $this->uploadedFile = null;
            $this->currentMembershipId = null;
            $this->loadMembershipData();
            
            $this->dispatch('success', message: 'Bukti transfer berhasil diupload. Admin akan segera memverifikasi.');
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Gagal upload bukti: ' . $e->getMessage());
        } finally {
            $this->isUploading = false;
        }
    }

    public function renewMembership()
    {
        if (!$this->activeMembership) {
            $this->dispatch('error', message: 'Anda tidak memiliki membership premium aktif untuk diperpanjang.');
            return;
        }

        $user = auth()->user();
        
        // Check if already has pending renewal
        $pendingRenewal = $user->premiumMemberships()
            ->where('status', 'pending')
            ->where('created_at', '>', now()->subDay())
            ->first();

        if ($pendingRenewal) {
            $this->dispatch('error', message: 'Anda sudah memiliki perpanjangan membership yang menunggu verifikasi.');
            return;
        }

        // Create new membership for renewal
        $membership = $user->premiumMemberships()->create([
            'price' => self::MEMBERSHIP_PRICE,
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
        ]);

        $this->currentMembershipId = $membership->id;
        $this->showUploadModal = true;
        
        $this->dispatch('success', message: 'Perpanjangan membership dibuat. Silakan upload bukti transfer.');
    }

    public function cancelUpload()
    {
        $this->showUploadModal = false;
        $this->uploadedFile = null;
        $this->currentMembershipId = null;
    }

    public function render()
    {
        return view('livewire.dashboard.premium-membership-purchase');
    }
}
