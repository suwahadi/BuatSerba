<div class="font-['Poppins']">
    <h1 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1zm-5 8.274l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L5 10.274zm10 0l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L15 10.274z" clip-rule="evenodd"/>
        </svg>
        Premium Membership
    </h1>

    <!-- Active Membership Banner -->
    @if($activeMembership)
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-300 rounded-lg p-6 mb-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-grow">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1zm-5 8.274l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L5 10.274zm10 0l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L15 10.274z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-lg font-bold text-purple-900">MEMBERSHIP AKTIF</span>
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
                <button wire:click="$set('showRenewalConfirmModal', true)"
                        type="button"
                        class="flex-shrink-0 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-all duration-200 whitespace-nowrap cursor-pointer">
                    Perpanjang
                </button>
            </div>
        </div>
    @elseif($pendingMembership)
        <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 border border-yellow-300 rounded-lg p-6 mb-6">
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
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
                <svg class="w-8 h-8 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-bold text-gray-900 text-sm">1% Cashback Instant</p>
                    <p class="text-xs text-gray-600 mt-1">Dapatkan 1% cashback untuk setiap pembelian langsung</p>
                </div>
            </div>
        </div>

        <!-- Benefit 2 -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-start gap-3">
                <svg class="w-8 h-8 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <div>
                    <p class="font-bold text-gray-900 text-sm">Berlaku 1 Tahun</p>
                    <p class="text-xs text-gray-600 mt-1">Nikmati benefit selama 12 bulan penuh</p>
                </div>
            </div>
        </div>

        <!-- Benefit 3 -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-start gap-3">
                <svg class="w-8 h-8 text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                </svg>
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
                        <span class="font-medium">Pembayaran aman</span> dan terpercaya. Bukti transfer akan diverifikasi oleh admin.
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
                <div class="space-y-2">
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
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click="$set('showPurchaseModal', false)">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6" wire:click.stop>
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-10 h-10 text-purple-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1zm-5 8.274l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L5 10.274zm10 0l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L15 10.274z" clip-rule="evenodd"/>
                    </svg>
                    <h2 class="text-xl font-bold text-gray-900">Bergabung Premium Membership</h2>
                </div>
                
                <div class="mb-6">
                    <p class="text-gray-700 mb-4">Apakah Anda yakin ingin bergabung dengan Premium Membership?</p>
                    
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-purple-700 font-medium">Total Pembayaran</span>
                            <span class="text-lg font-bold text-purple-900">Rp 100.000</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-purple-700 font-medium">Durasi</span>
                            <span class="text-sm font-bold text-purple-900">1 Tahun (365 hari)</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-purple-200">
                            <span class="text-sm text-purple-700 font-medium">Metode Pembayaran</span>
                            <span class="text-sm font-bold text-purple-900">Transfer Bank</span>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-4">
                        <p class="text-xs text-blue-800">
                            <strong>Informasi:</strong> Setelah konfirmasi, Anda akan diarahkan untuk upload bukti transfer. Benefit premium akan aktif setelah pembayaran diverifikasi oleh admin.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button"
                            wire:click="$set('showPurchaseModal', false)"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold py-3 px-4 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="purchasePremium"
                            class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded-lg shadow-sm transition-colors">
                        Ya, Bergabung
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Renewal Confirmation Modal -->
    @if($showRenewalConfirmModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click="$set('showRenewalConfirmModal', false)">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6" wire:click.stop>
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-10 h-10 text-purple-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1zm-5 8.274l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L5 10.274zm10 0l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L15 10.274z" clip-rule="evenodd"/>
                    </svg>
                    <h2 class="text-xl font-bold text-gray-900">Perpanjang Premium Membership</h2>
                </div>
                
                <div class="mb-6">
                    <p class="text-gray-700 mb-4">Apakah Anda yakin ingin memperpanjang membership premium Anda?</p>
                    
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-purple-700 font-medium">Biaya Perpanjangan</span>
                            <span class="text-lg font-bold text-purple-900">Rp 100.000</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-purple-700 font-medium">Durasi Tambahan</span>
                            <span class="text-sm font-bold text-purple-900">1 Tahun (365 hari)</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-purple-200">
                            <span class="text-sm text-purple-700 font-medium">Status Saat Ini</span>
                            <span class="text-sm font-bold text-green-600">Aktif</span>
                        </div>
                        @if($activeMembership)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-purple-700 font-medium">Berakhir Saat Ini</span>
                            <span class="text-sm font-bold text-purple-900">{{ $activeMembership->expires_at?->format('d M Y') }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-4">
                        <p class="text-xs text-blue-800">
                            <strong>Informasi:</strong> Setelah konfirmasi, Anda akan diarahkan untuk upload bukti transfer. Masa aktif akan diperpanjang setelah pembayaran diverifikasi oleh admin.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button"
                            wire:click="$set('showRenewalConfirmModal', false)"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold py-3 px-4 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="renewMembership"
                            class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded-lg shadow-sm transition-colors">
                        Ya, Perpanjang
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Upload Proof Modal -->
    @if($showUploadModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click="cancelUpload">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6" wire:click.stop>
                <h2 class="text-lg font-bold text-gray-900 mb-4">Upload Bukti Transfer</h2>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-xs text-blue-800 font-medium mb-2">Informasi Pembayaran:</p>
                    <div class="space-y-1 text-xs text-blue-800">
                        <div><strong>Bank:</strong> {{ global_config('manual_bank_name') ?? 'BCA' }}</div>
                        <div><strong>No. Rek:</strong> {{ global_config('manual_bank_account_number') ?? '-' }}</div>
                        <div><strong>A/N:</strong> {{ global_config('manual_bank_account_name') ?? '-' }}</div>
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
