<?php

namespace App\Livewire\Dashboard;

use App\Models\GlobalConfig;
use App\Models\PremiumMembership;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;
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
    public $showRenewalConfirmModal = false;
    public $showUploadModal = false;
    public $currentMembershipId;

    public $uploadedFile;
    public $isUploading = false;
    
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
        
        $this->activeMembership = $user->activePremiumMembership()->first();

        $this->pendingMembership = $user->premiumMemberships()
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($this->activeMembership) {
            $this->daysRemaining = $this->activeMembership->daysRemaining() ?? 0;
        }
    }

    public function purchasePremium()
    {
        $user = auth()->user();

        $existingPending = $user->premiumMemberships()
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            $this->dispatch('error', message: 'Anda sudah memiliki pembelian premium yang menunggu verifikasi.');
            return;
        }

        $membership = $user->premiumMemberships()->create([
            'price' => GlobalConfig::getPremiumMembershipPrice(),
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
            'uploadedFile' => 'required|file|mimes:jpg,jpeg,png,pdf,webp|max:5048',
        ], [
            'uploadedFile.required' => 'Silakan pilih file bukti transfer.',
            'uploadedFile.mimes' => 'Format file harus JPG, JPEG, PNG, PDF, atau WebP.',
            'uploadedFile.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        if (!$this->currentMembershipId) {
            $this->dispatch('error', message: 'Membership tidak ditemukan.');
            return;
        }

        $this->isUploading = true;

        try {
            $membership = PremiumMembership::findOrFail($this->currentMembershipId);

            if ($membership->user_id !== auth()->id()) {
                $this->dispatch('error', message: 'Unauthorized');
                $this->isUploading = false;
                return;
            }

            if ($membership->payment_proof_path) {
                Storage::disk('public')->delete($membership->payment_proof_path);
            }

            $file = $this->uploadedFile;
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

        $pendingRenewal = $user->premiumMemberships()
            ->where('status', 'pending')
            ->where('created_at', '>', now()->subDay())
            ->first();

        if ($pendingRenewal) {
            $this->dispatch('error', message: 'Anda sudah memiliki perpanjangan membership yang menunggu verifikasi.');
            return;
        }

        $membership = $user->premiumMemberships()->create([
            'price' => GlobalConfig::getPremiumMembershipPrice(),
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
        ]);

        $this->currentMembershipId = $membership->id;
        $this->showRenewalConfirmModal = false;
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
