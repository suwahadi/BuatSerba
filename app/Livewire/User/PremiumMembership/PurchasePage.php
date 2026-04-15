<?php

namespace App\Livewire\User\PremiumMembership;

use App\Models\GlobalConfig;
use App\Models\PremiumMembership;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Beli Premium Membership')]
class PurchasePage extends Component
{
    public $price;
    public $activeMembership = null;
    public $daysRemaining = null;
    public $pendingMembership = null;

    public $showPurchaseModal = false;
    public $showUploadModal = false;
    public $showSuccessModal = false;

    public $membershipId = null;
    public $proofFile = null;
    public $uploadLoading = false;
    public $uploadError = null;
    public $termsAccepted = false;

    public function mount()
    {
        $this->price = GlobalConfig::getPremiumMembershipPrice();
        
        $user = Auth::user();
        $this->activeMembership = $user->activePremiumMembership()->first();
        
        if ($this->activeMembership) {
            $this->daysRemaining = $this->activeMembership->daysRemaining();
        }

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

        $existingPending = $user->premiumMemberships()
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            $this->dispatch('notify', type: 'warning', message: 'Anda sudah memiliki pembelian premium yang menunggu verifikasi. Silakan upload bukti transfer terlebih dahulu.');
            return;
        }

        try {
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
            'proofFile' => 'required|file|mimes:jpg,jpeg,png,pdf,webp|max:5048',
        ], [
            'proofFile.required' => 'Silakan pilih file bukti transfer.',
            'proofFile.mimes' => 'Format file harus JPG, JPEG, PNG, PDF, atau WebP.',
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

            if ($membership->user_id !== $user->id) {
                throw new \Exception('Unauthorized');
            }

            if ($membership->status !== 'pending') {
                throw new \Exception('Hanya pembelian yang pending yang bisa upload bukti transfer.');
            }

            if ($membership->payment_proof_path) {
                Storage::disk('public')->delete($membership->payment_proof_path);
            }

            $file = $this->proofFile;
            $extension = strtolower($file->getClientOriginalExtension());
            
            $timestamp = time();
            $uniqueName = $timestamp . '.' . $extension;
            
            $tempPath = $file->storeAs('temp-premium-proof', $uniqueName, 'public');
            $fullTempPath = storage_path('app/public/' . $tempPath);
            
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                try {
                    $image = Image::read($fullTempPath);
                    $image->orient();
                    
                    $width = $image->width();
                    $height = $image->height();
                    $maxSide = max($width, $height);
                    
                    if ($maxSide > 1200) {
                        $ratio = 1200 / $maxSide;
                        $newWidth = (int) round($width * $ratio);
                        $newHeight = (int) round($height * $ratio);
                        $image->resize($newWidth, $newHeight);
                    }
                    
                    $encoded = $image->encode(new WebpEncoder(quality: 80));
                    $webpData = $encoded->toString();
                    
                    $webpFilename = $timestamp . '.webp';
                    $finalPath = 'premium-proof/' . $webpFilename;
                    
                    Storage::disk('public')->put($finalPath, $webpData);
                    
                    Storage::disk('public')->delete($tempPath);
                    
                    $path = $finalPath;
                } catch (\Exception $e) {
                    $path = $tempPath;
                }
            } else {
                $finalPath = 'premium-proof/' . $uniqueName;
                Storage::disk('public')->move($tempPath, $finalPath);
                $path = $finalPath;
            }

            $membership->update(['payment_proof_path' => $path]);

            $this->showUploadModal = false;
            $this->showSuccessModal = true;
            $this->proofFile = null;

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

        $pendingRenewal = $user->premiumMemberships()
            ->where('status', 'pending')
            ->where('created_at', '>', now()->subDay())
            ->first();

        if ($pendingRenewal) {
            $this->dispatch('notify', type: 'warning', message: 'Anda sudah memiliki perpanjangan membership yang menunggu verifikasi.');
            return;
        }

        try {
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
