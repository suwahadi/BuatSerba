<?php

namespace App\Livewire\Dashboard;

use App\Models\GlobalConfig;
use App\Models\PremiumMembership;
use App\Services\PremiumMembershipPaymentService;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.dashboard')]
#[Title('Premium Membership - BuatSerba')]
class PremiumMembershipPurchase extends Component
{
    use WithFileUploads;
    public $activeMembership;
    public $pendingMembership;
    public $daysRemaining = 0;
    public $showPurchaseModal = false;
    public $showRenewalConfirmModal = false;
    public $showUploadModal = false;
    public $showPaymentMethodModal = false;
    public $currentMembershipId;
    public $selectedPaymentMethod = null;
    public $paymentInstructions = null;
    public $paymentOrderId = null;

    public $uploadedFile;
    public $isUploading = false;
    public $isProcessingPayment = false;
    public $selectedMethodLoading = null;
    
    public $membershipHistory;
    public $qrCodeImage = null;
    public $showDetailModal = false;
    public $selectedMembership = null;
    
    public const MEMBERSHIP_DURATION_DAYS = 365;

    public function mount()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $this->loadMembershipData();
        $this->loadMembershipHistory();
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

    public function loadMembershipHistory()
    {
        $user = auth()->user();
        
        $this->membershipHistory = $user->premiumMemberships()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    public function purchasePremium()
    {
        $user = auth()->user();

        $existingPending = $user->premiumMemberships()
            ->where('status', 'pending')
            ->where(function($query) {
                $query->whereNull('transaction_status')
                    ->orWhere('transaction_status', 'pending');
            })
            ->first();

        if ($existingPending) {
            $this->dispatch('error', message: 'Anda sudah memiliki pembelian premium yang menunggu pembayaran.');
            return;
        }

        $this->showPurchaseModal = false;
        $this->showPaymentMethodModal = true;
        
        $this->dispatch('success', message: 'Silakan pilih metode pembayaran.');
    }

    public function selectPaymentMethod($method)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                $this->dispatch('error', message: 'Anda harus login terlebih dahulu.');
                return;
            }

            // Check for existing pending membership
            $existingPending = $user->premiumMemberships()
                ->where('status', 'pending')
                ->where(function($query) {
                    $query->whereNull('transaction_status')
                        ->orWhere('transaction_status', 'pending');
                })
                ->first();

            if ($existingPending) {
                $this->dispatch('error', message: 'Anda sudah memiliki pembelian premium yang menunggu pembayaran.');
                return;
            }

            // Set loading state for this method
            $this->selectedMethodLoading = $method;

            // Create membership record first
            $membership = $user->premiumMemberships()->create([
                'price' => GlobalConfig::getPremiumMembershipPrice(),
                'status' => 'pending',
                'payment_method' => $method === 'manual-transfer' ? 'bank_transfer' : 'midtrans',
            ]);

            $this->currentMembershipId = $membership->id;
            $this->selectedPaymentMethod = $method;
            $this->showPaymentMethodModal = false;
            
            if ($method === 'manual-transfer') {
                $this->createManualTransferMembership();
            } else {
                $this->processPaymentWithMidtrans();
            }
        } catch (\Exception $e) {
            // \Log::error('Error in selectPaymentMethod: ' . $e->getMessage(), [
            //     'method' => $method,
            //     'trace' => $e->getTraceAsString()
            // ]);
            $this->dispatch('error', message: 'Terjadi kesalahan: ' . $e->getMessage());
        } finally {
            // Clear loading state
            $this->selectedMethodLoading = null;
        }
    }

    protected function createManualTransferMembership()
    {
        // Membership already created in selectPaymentMethod
        $this->showUploadModal = true;
        $this->loadMembershipData();
        
        $this->dispatch('success', message: 'Pembelian premium dibuat. Silakan upload bukti transfer.');
    }

    public function processPaymentWithMidtrans()
    {
        try {
            if (!$this->currentMembershipId || !$this->selectedPaymentMethod) {
                // \Log::error('Missing payment data', [
                //     'membership_id' => $this->currentMembershipId,
                //     'payment_method' => $this->selectedPaymentMethod
                // ]);
                $this->dispatch('error', message: 'Data pembayaran tidak lengkap.');
                return;
            }

            $this->isProcessingPayment = true;

            // \Log::info('Processing Midtrans payment', [
            //     'membership_id' => $this->currentMembershipId,
            //     'payment_method' => $this->selectedPaymentMethod
            // ]);

            $membership = PremiumMembership::findOrFail($this->currentMembershipId);

            if ($membership->user_id !== auth()->id()) {
                // \Log::warning('Unauthorized payment attempt', [
                //     'membership_id' => $this->currentMembershipId,
                //     'user_id' => auth()->id()
                // ]);
                $this->dispatch('error', message: 'Unauthorized');
                $this->isProcessingPayment = false;
                return;
            }

            $paymentService = new PremiumMembershipPaymentService();
            $result = $paymentService->createTransaction($membership, $this->selectedPaymentMethod);

            if ($result['success']) {
                $this->paymentInstructions = $result['payment_instructions'];
                $this->paymentOrderId = $result['order_id'];
                $this->showPaymentMethodModal = false;
                $this->showUploadModal = true;
                $this->loadMembershipData();
                
                // \Log::info('Payment transaction created successfully', [
                //     'order_id' => $result['order_id'],
                //     'payment_instructions' => $result['payment_instructions']
                // ]);
                
                if (isset($result['payment_instructions']['type']) && $result['payment_instructions']['type'] === 'qris') {
                    $this->generateQrCode($result['payment_instructions']['qr_string'] ?? null);
                }
                
                $this->dispatch('success', message: 'Transaksi berhasil dibuat. Silakan selesaikan pembayaran.');
            } else {
                // \Log::error('Payment transaction failed', [
                //     'message' => $result['message'] ?? 'Unknown error'
                // ]);
                $this->dispatch('error', message: $result['message'] ?? 'Gagal memproses pembayaran.');
            }

        } catch (\Exception $e) {
            // \Log::error('Premium membership payment error: ' . $e->getMessage(), [
            //     'trace' => $e->getTraceAsString(),
            //     'membership_id' => $this->currentMembershipId,
            //     'payment_method' => $this->selectedPaymentMethod
            // ]);
            $this->dispatch('error', message: 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage());
        } finally {
            $this->isProcessingPayment = false;
        }
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
            $this->paymentInstructions = null;
            $this->paymentOrderId = null;
            $this->selectedPaymentMethod = null;
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

        $this->showRenewalConfirmModal = false;
        $this->showPaymentMethodModal = true;
        
        $this->dispatch('success', message: 'Silakan pilih metode pembayaran untuk perpanjangan.');
    }

    public function cancelUpload()
    {
        $this->showUploadModal = false;
        $this->uploadedFile = null;
        $this->currentMembershipId = null;
        $this->paymentInstructions = null;
        $this->paymentOrderId = null;
        $this->selectedPaymentMethod = null;
        $this->qrCodeImage = null;
    }

    /**
     * Generate QR code image from QR string
     */
    public function generateQrCode(?string $qrString)
    {
        if (!$qrString) {
            $this->qrCodeImage = null;
            //\Log::warning('QR string is empty');
            return;
        }

        try {
            // \Log::info('Generating QR code', ['qr_string_length' => strlen($qrString)]);
            
            $options = new \chillerlan\QRCode\QROptions([
                'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
                'scale' => 8,
                'imageBase64' => true,
                'imageTransparent' => false,
            ]);
            
            $qrcode = new \chillerlan\QRCode\QRCode($options);
            $this->qrCodeImage = $qrcode->render($qrString);
            
            // \Log::info('QR code generated successfully', [
            //     'image_length' => strlen($this->qrCodeImage)
            // ]);
        } catch (\Exception $e) {
            // \Log::error('QR code generation failed: ' . $e->getMessage(), [
            //     'trace' => $e->getTraceAsString()
            // ]);
            $this->qrCodeImage = null;
        }
    }

    public function showMembershipDetail($membershipId)
    {
        $membership = PremiumMembership::find($membershipId);
        
        if (!$membership || $membership->user_id !== auth()->id()) {
            $this->dispatch('error', message: 'Membership tidak ditemukan.');
            return;
        }
        
        $this->selectedMembership = $membership;
        $this->showDetailModal = true;
        
        if ($membership->status === 'pending' && $membership->payment_method === 'midtrans') {
            $this->paymentInstructions = $membership->payment_instructions;
            $this->paymentOrderId = $membership->order_id;
            
            if (isset($this->paymentInstructions['type']) && $this->paymentInstructions['type'] === 'qris') {
                $this->generateQrCode($this->paymentInstructions['qr_string'] ?? null);
            }
        }
    }
    
    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedMembership = null;
        $this->paymentInstructions = null;
        $this->paymentOrderId = null;
        $this->qrCodeImage = null;
    }

    public function render()
    {
        return view('livewire.dashboard.premium-membership-purchase');
    }
}
