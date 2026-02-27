<div>
    <h1 class="text-xl font-bold text-gray-900 mb-4">💎 Premium Membership</h1>

    <!-- Active Membership Banner -->
    @if($activeMembership)
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-300 rounded-lg p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-lg font-bold text-purple-900">🌟 MEMBERSHIP AKTIF</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-3">
                        <div>
                            <p class="text-xs text-purple-700 font-medium">Status</p>
                            <p class="text-sm font-bold text-purple-900 mt-1">Aktif</p>
                        </div>
                        <div>
                            <p class="text-xs text-purple-700 font-medium">Dimulai</p>
                            <p class="text-sm font-bold text-purple-900 mt-1">
                                {{ $activeMembership->started_at?->format('d M Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-purple-700 font-medium">Berakhir</p>
                            <p class="text-sm font-bold text-purple-900 mt-1">
                                {{ $activeMembership->expires_at?->format('d M Y') }}
                            </p>
                        </div>
                        <div class="text-right md:text-left">
                            <p class="text-xs text-purple-700 font-medium">Sisa Waktu</p>
                            <p class="text-sm font-bold text-purple-900 mt-1">{{ $daysRemaining }} hari</p>
                        </div>
                    </div>
                </div>
                <button wire:click="renewMembership"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-all duration-200 whitespace-nowrap">
                    Perpanjang
                </button>
            </div>
        </div>
    @elseif($pendingMembership)
        <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 border border-yellow-300 rounded-lg p-6 mb-6">
            <div class="flex items-center gap-3">
                <div class="text-2xl">⏳</div>
                <div>
                    <p class="font-bold text-yellow-900">Menunggu Verifikasi</p>
                    <p class="text-sm text-yellow-800 mt-1">
                        Bukti transfer Anda sedang diverifikasi oleh admin. Biasanya selesai dalam 1x24 jam.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Benefits Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Benefit 1 -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-start gap-3">
                <div class="text-2xl">💰</div>
                <div>
                    <p class="font-bold text-gray-900 text-sm">1% Cashback Instant</p>
                    <p class="text-xs text-gray-600 mt-1">Dapatkan 1% cashback untuk setiap pembelian langsung</p>
                </div>
            </div>
        </div>

        <!-- Benefit 2 -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-start gap-3">
                <div class="text-2xl">📅</div>
                <div>
                    <p class="font-bold text-gray-900 text-sm">Berlaku 1 Tahun</p>
                    <p class="text-xs text-gray-600 mt-1">Nikmati benefit selama 12 bulan penuh</p>
                </div>
            </div>
        </div>

        <!-- Benefit 3 -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-start gap-3">
                <div class="text-2xl">🎁</div>
                <div>
                    <p class="font-bold text-gray-900 text-sm">Akses Penawaran Eksklusif</p>
                    <p class="text-xs text-gray-600 mt-1">Dapatkan akses produk dan promo untuk member premium</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-12">
            <!-- Left: Pricing Info -->
            <div>
                <h2 class="text-lg font-bold text-gray-900 mb-6">Paket Premium Membership</h2>
                
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Harga</span>
                        <span class="text-2xl font-bold text-purple-600">Rp 100.000</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Durasi</span>
                        <span class="text-gray-900 font-bold">1 Tahun (365 hari)</span>
                    </div>
                    <div class="flex justify-between items-center pb-3">
                        <span class="text-gray-600 font-medium">Tipe Pembayaran</span>
                        <span class="text-gray-900 font-bold">Transfer Bank</span>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-xs text-green-800">
                        ✅ <span class="font-medium">Pembayaran aman</span> dan terpercaya. Bukti transfer akan diverifikasi oleh admin.
                    </p>
                </div>

                @if(!$activeMembership && !$pendingMembership)
                    <button wire:click="$set('showPurchaseModal', true)"
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded-lg shadow-sm transition-all duration-200">
                        Beli Premium Sekarang
                    </button>
                @endif
            </div>

            <!-- Right: Feature List -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-6">Fitur & Keuntungan</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="text-green-600 font-bold mt-0.5">✓</span>
                        <span class="text-gray-700 text-sm">1% Cashback instant untuk setiap pembelian</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-green-600 font-bold mt-0.5">✓</span>
                        <span class="text-gray-700 text-sm">Berlaku untuk semua produk di BuatSerba</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-green-600 font-bold mt-0.5">✓</span>
                        <span class="text-gray-700 text-sm">Cashback langsung masuk ke saldo Anda</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-green-600 font-bold mt-0.5">✓</span>
                        <span class="text-gray-700 text-sm">Saldo dapat digunakan untuk pembelian berikutnya</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-green-600 font-bold mt-0.5">✓</span>
                        <span class="text-gray-700 text-sm">Akses ke private sale dan penawaran eksklusif</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-green-600 font-bold mt-0.5">✓</span>
                        <span class="text-gray-700 text-sm">Proritas customer service 24/7</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-green-600 font-bold mt-0.5">✓</span>
                        <span class="text-gray-700 text-sm">Masa berlaku 1 tahun penuh</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Confirmation Modal -->
    @if($showPurchaseModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Pembelian</h2>
                
                <div class="space-y-4 mb-6 pb-6 border-b border-gray-200">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Paket Premium Membership</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-900">Total Pembayaran</span>
                        <span class="text-2xl font-bold text-purple-600">Rp 100.000</span>
                    </div>
                    <p class="text-xs text-gray-600 pt-2">
                        Durasi: 1 Tahun (365 hari) | Berlaku otomatis setelah verifikasi pembayaran
                    </p>
                </div>

                <div class="flex items-start gap-3 mb-6 p-3 bg-blue-50 rounded">
                    <span class="text-blue-600 mt-0.5">ℹ️</span>
                    <p class="text-xs text-blue-800">
                        Setelah klik "Lanjutkan", Anda akan dimintakan untuk mengupload bukti transfer dan admin akan memverifikasinya.
                    </p>
                </div>

                <div class="flex gap-3">
                    <button wire:click="$set('showPurchaseModal', false)"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold py-2.5 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button wire:click="purchasePremium"
                            class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 rounded-lg transition-colors">
                        Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Upload Proof Modal -->
    @if($showUploadModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Upload Bukti Transfer</h2>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-xs text-blue-800 font-medium mb-2">Informasi Pembayaran:</p>
                    <div class="space-y-1 text-xs text-blue-800">
                        <div><strong>Bank:</strong> BCA</div>
                        <div><strong>No. Rek:</strong> 123456789</div>
                        <div><strong>A/N:</strong> PT Buat Serba</div>
                    </div>
                </div>

                <form wire:submit="saveUploadedFile" class="space-y-4">
                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            Bukti Transfer
                        </label>
                        <div class="relative">
                            <input type="file" wire:model="uploadedFile" 
                                   accept="image/jpeg,image/png,image/jpg"
                                   class="sr-only"
                                   id="fileInput">
                            <label for="fileInput"
                                   class="block w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-gray-400 transition-colors text-center">
                                @if($uploadedFile)
                                    <div class="text-sm">
                                        <span class="font-medium text-green-600">✓ File dipilih:</span>
                                        <p class="text-gray-600 mt-1">{{ $uploadedFile->getClientOriginalName() }}</p>
                                    </div>
                                @else
                                    <div>
                                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-600">
                                            <span class="font-medium">Klik untuk upload</span> atau drag file
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500">JPG, PNG (Max 5 MB)</p>
                                    </div>
                                @endif
                            </label>
                        </div>
                        @error('uploadedFile')
                            <p class="text-red-600 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-xs text-yellow-800">
                            <strong>⚠️ Pastikan:</strong> Bukti transfer jelas menampilkan nomor referensi dan nominal Rp 100.000
                        </p>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button"
                                wire:click="cancelUpload"
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold py-2.5 rounded-lg transition-colors"
                                {{ $isUploading ? 'disabled' : '' }}>
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                {{ $isUploading ? 'disabled' : '' }}>
                            @if($isUploading)
                                <span class="inline-block animate-spin mr-2">⏳</span> Uploading...
                            @else
                                Upload
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
