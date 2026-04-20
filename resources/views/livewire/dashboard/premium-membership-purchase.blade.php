<div class="font-['Poppins'] relative">
    <!-- Global Loading Overlay -->
    @if($isProcessingPayment)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[100]">
            <div class="bg-white rounded-lg shadow-xl p-8 max-w-sm w-full mx-4 text-center">
                <svg class="animate-spin h-12 w-12 text-green-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-900 font-bold text-lg mb-2">Memproses Pembayaran...</p>
                <p class="text-gray-600 text-sm">Mohon tunggu, sedang menghubungkan ke gateway pembayaran</p>
            </div>
        </div>
    @endif

    <h1 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 13l5-5 5 5M7 11l5-5 5 5M12 2l10 6v8l-10 6-10-6V8l10-6z"/>
    </svg>
        Premium Membership
    </h1>

    <!-- Active Membership Banner -->
    @if($activeMembership)
        <div class="bg-gradient-to-r from-green-50 to-green-100 border border-green-300 rounded-lg p-6 mb-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-grow">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-lg font-bold text-green-900">MEMBERSHIP AKTIF</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-3">
                        <div>
                            <p class="text-xs text-green-700 font-medium">Status</p>
                            <p class="text-sm font-bold text-green-900 mt-1">Aktif</p>
                        </div>
                        <div>
                            <p class="text-xs text-green-700 font-medium">Dimulai</p>
                            <p class="text-sm font-bold text-green-900 mt-1">
                                {{ $activeMembership->started_at?->format('d M Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-green-700 font-medium">Berakhir</p>
                            <p class="text-sm font-bold text-green-900 mt-1">
                                {{ $activeMembership->expires_at?->format('d M Y') }}
                            </p>
                        </div>
                        <div class="text-right md:text-left">
                            <p class="text-xs text-green-700 font-medium">Sisa Waktu</p>
                            <p class="text-sm font-bold text-green-900 mt-1">{{ $daysRemaining }} hari</p>
                        </div>
                    </div>
                </div>
                <button wire:click="$set('showRenewalConfirmModal', true)"
                        type="button"
                        class="flex-shrink-0 bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-all duration-200 whitespace-nowrap cursor-pointer">
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
                    <p class="font-bold text-gray-900 text-sm">{{ global_config('cashback', 1) }}% Cashback Instant</p>
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
                <svg class="w-8 h-8 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <span class="text-2xl font-bold text-green-600">Rp {{ number_format(global_config('premium_membership_price', 100000), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Durasi</span>
                        <span class="text-gray-900 font-bold">1 Tahun (365 hari)</span>
                    </div>
                </div>

                @if(!$activeMembership && !$pendingMembership)
                    <button wire:click="$set('showPurchaseModal', true)"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-sm transition-all duration-200 cursor-pointer">
                        Upgrade Premium Sekarang
                    </button>
                @endif
            </div>

            <!-- Right: Feature List -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-3">Fitur & Keuntungan</h3>
                <div class="">
                    <div class="flex items-start gap-3">
                        <span class="text-green-600 font-bold mt-0.5">✓</span>
                        <span class="text-gray-700 text-sm">{{ global_config('cashback', 1) }}% Cashback instant untuk setiap pembelian</span>
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
                        <span class="text-gray-700 text-sm">Masa berlaku 1 tahun</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Confirmation Modal -->
    @if($showPurchaseModal)
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:click="$set('showPurchaseModal', false)">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" wire:click.stop>
                <div class="flex items-center gap-3 mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Bergabung Premium Membership</h2>
                </div>
                
                <div class="mb-6">
                    <p class="text-gray-700 mb-4 text-sm">Apakah Anda yakin ingin bergabung dengan Premium Membership?</p>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-green-700 font-medium">Total Pembayaran</span>
                            <span class="text-lg font-bold text-green-900">Rp {{ number_format(global_config('premium_membership_price', 100000), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-green-700 font-medium">Durasi</span>
                            <span class="text-sm font-bold text-green-900">1 Tahun (365 hari)</span>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-4">
                        <p class="text-xs text-gray-800">
                            <strong>Informasi:</strong> Benefit premium akan aktif setelah pembayaran berhasil dikonfirmasi.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button"
                            wire:click="$set('showPurchaseModal', false)"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="purchasePremium"
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-sm transition-colors cursor-pointer">
                        Bergabung
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Renewal Confirmation Modal -->
    @if($showRenewalConfirmModal)
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:click="$set('showRenewalConfirmModal', false)">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" wire:click.stop>
                <div class="flex items-center gap-3 mb-4">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 13l5-5 5 5M7 11l5-5 5 5M12 2l10 6v8l-10 6-10-6V8l10-6z"/>
                </svg>
                    <h2 class="text-lg font-bold text-gray-900">Perpanjang Premium Membership</h2>
                </div>
                
                <div class="mb-6">
                    <p class="text-gray-700 mb-4 text-sm">Apakah Anda yakin ingin memperpanjang membership premium Anda?</p>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-green-700 font-medium">Biaya Perpanjangan</span>
                            <span class="text-lg font-bold text-green-900">Rp {{ number_format(global_config('premium_membership_price', 100000), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-green-700 font-medium">Durasi Tambahan</span>
                            <span class="text-sm font-bold text-green-900">1 Tahun (365 hari)</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-green-200">
                            <span class="text-sm text-green-700 font-medium">Status Saat Ini</span>
                            <span class="text-sm font-bold text-green-600">Aktif</span>
                        </div>
                        @if($activeMembership)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-green-700 font-medium">Berakhir Saat Ini</span>
                            <span class="text-sm font-bold text-green-900">{{ $activeMembership->expires_at?->format('d M Y') }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-4">
                        <p class="text-xs text-blue-800">
                            <strong>Informasi:</strong> Setelah konfirmasi, Anda akan memilih metode pembayaran. Masa aktif akan diperpanjang setelah pembayaran berhasil.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button"
                            wire:click="$set('showRenewalConfirmModal', false)"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="renewMembership"
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-sm transition-colors cursor-pointer">
                        Perpanjang
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Payment Method Selection Modal -->
    @if($showPaymentMethodModal)
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:click="$set('showPaymentMethodModal', false)">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto" wire:click.stop>
                <div class="flex items-center gap-3 mb-6">
                    <div>
                        <h2 class="text-md font-bold text-gray-900">Pilih Metode Pembayaran</h2>
                        <p class="text-sm text-gray-600">Total: Rp {{ number_format(global_config('premium_membership_price', 100000), 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Virtual Account Section -->
                    <div>
                        <h3 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Virtual Account</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            {{-- <button wire:click="selectPaymentMethod('bank-transfer-bca')"
                                    wire:loading.attr="disabled"
                                    wire:target="selectPaymentMethod"
                                    class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:border-gray-200 disabled:hover:bg-white relative">
                                @if($selectedMethodLoading === 'bank-transfer-bca')
                                    <div class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-lg flex items-center justify-center z-10">
                                        <svg class="animate-spin h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center p-1">
                                    <img src="/storage/static/bca.png" alt="BCA" class="w-full h-full object-contain">
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-gray-900 text-sm">BCA Virtual Account</p>
                                    <p class="text-xs text-gray-600">Bayar via ATM/Mobile Banking</p>
                                </div>
                            </button> --}}

                            <button wire:click="selectPaymentMethod('bank-transfer-bni')"
                                    wire:loading.attr="disabled"
                                    wire:target="selectPaymentMethod"
                                    class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:border-gray-200 disabled:hover:bg-white relative">
                                @if($selectedMethodLoading === 'bank-transfer-bni')
                                    <div class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-lg flex items-center justify-center z-10">
                                        <svg class="animate-spin h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center p-1">
                                    <img src="/storage/static/bni.png" alt="BNI" class="w-full h-full object-contain">
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-gray-900 text-sm">BNI Virtual Account</p>
                                    <p class="text-xs text-gray-600">Bayar via ATM/Mobile Banking</p>
                                </div>
                            </button>

                            <button wire:click="selectPaymentMethod('bank-transfer-bri')"
                                    wire:loading.attr="disabled"
                                    wire:target="selectPaymentMethod"
                                    class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:border-gray-200 disabled:hover:bg-white relative">
                                @if($selectedMethodLoading === 'bank-transfer-bri')
                                    <div class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-lg flex items-center justify-center z-10">
                                        <svg class="animate-spin h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center p-1">
                                    <img src="/storage/static/bri.png" alt="BRI" class="w-full h-full object-contain">
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-gray-900 text-sm">BRI Virtual Account</p>
                                    <p class="text-xs text-gray-600">Bayar via ATM/Mobile Banking</p>
                                </div>
                            </button>

                            <button wire:click="selectPaymentMethod('bank-transfer-mandiri')"
                                    wire:loading.attr="disabled"
                                    wire:target="selectPaymentMethod"
                                    class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:border-gray-200 disabled:hover:bg-white relative">
                                @if($selectedMethodLoading === 'bank-transfer-mandiri')
                                    <div class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-lg flex items-center justify-center z-10">
                                        <svg class="animate-spin h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center p-1">
                                    <img src="/storage/static/mandiri.jpg" alt="Mandiri" class="w-full h-full object-contain">
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-gray-900 text-sm">Mandiri Bill Payment</p>
                                    <p class="text-xs text-gray-600">Bayar via ATM/Mobile Banking</p>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- E-Wallet Section -->
                    <!-- <div>
                        <h3 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">E-Wallet</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <button wire:click="selectPaymentMethod('e-wallet-gopay')"
                                    class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all cursor-pointer">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <span class="text-blue-600 font-bold text-xs">GOPAY</span>
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-gray-900 text-sm">GoPay</p>
                                    <p class="text-xs text-gray-600">Bayar via aplikasi Gojek</p>
                                </div>
                            </button>

                            <button wire:click="selectPaymentMethod('e-wallet-shopeepay')"
                                    class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all cursor-pointer">
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <span class="text-orange-600 font-bold text-xs">SHOPEE</span>
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-gray-900 text-sm">ShopeePay</p>
                                    <p class="text-xs text-gray-600">Bayar via aplikasi Shopee</p>
                                </div>
                            </button>

                            <button wire:click="selectPaymentMethod('e-wallet-ovo')"
                                    class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all cursor-pointer">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <span class="text-purple-600 font-bold text-xs">OVO</span>
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-gray-900 text-sm">OVO</p>
                                    <p class="text-xs text-gray-600">Bayar via aplikasi OVO</p>
                                </div>
                            </button>

                            <button wire:click="selectPaymentMethod('e-wallet-dana')"
                                    class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all cursor-pointer">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <span class="text-blue-600 font-bold text-xs">DANA</span>
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-gray-900 text-sm">DANA</p>
                                    <p class="text-xs text-gray-600">Bayar via aplikasi DANA</p>
                                </div>
                            </button>
                        </div>
                    </div> -->

                    <!-- QRIS -->
                    <div>
                        <h3 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">QR Code</h3>
                        <div class="grid grid-cols-1 gap-3">
                            <button wire:click="selectPaymentMethod('qris')"
                                    wire:loading.attr="disabled"
                                    wire:target="selectPaymentMethod"
                                    class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:border-gray-200 disabled:hover:bg-white relative">
                                @if($selectedMethodLoading === 'qris')
                                    <div class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-lg flex items-center justify-center z-10">
                                        <svg class="animate-spin h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center p-1">
                                    <img src="/storage/static/qris.png" alt="QRIS" class="w-full h-full object-contain">
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-gray-900 text-sm">QRIS</p>
                                    <p class="text-xs text-gray-600">Scan QR dari semua e-wallet & mobile banking</p>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Manual Transfer Section -->
                    <div>
                        <h3 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Transfer Manual</h3>
                        <div class="grid grid-cols-1 gap-3">
                            <button wire:click="selectPaymentMethod('manual-transfer')"
                                    wire:loading.attr="disabled"
                                    wire:target="selectPaymentMethod"
                                    class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:border-gray-200 disabled:hover:bg-white relative">
                                @if($selectedMethodLoading === 'manual-transfer')
                                    <div class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-lg flex items-center justify-center z-10">
                                        <svg class="animate-spin h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center p-1">
                                    <img src="/storage/static/manual_bank_transfer.png" alt="Manual Transfer" class="w-full h-full object-contain">
                                </div>
                                <div class="text-left flex-1">
                                    <p class="font-bold text-gray-900 text-sm">Transfer Bank Manual</p>
                                    <p class="text-xs text-gray-600">Transfer ke rekening & upload bukti</p>
                                </div>
                                <span class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded font-medium">Manual</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-200">
                    <button type="button"
                            wire:click="$set('showPaymentMethodModal', false)"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Upload Proof Modal -->
    @if($showUploadModal)
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:click="cancelUpload">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto" wire:click.stop>
                <h2 class="text-lg font-bold text-gray-900 mb-4">Selesaikan Pembayaran</h2>
                
                @if($paymentInstructions && $paymentOrderId)
                    <!-- Payment Instructions Display -->
                    <div class="mb-6">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <p class="text-xs text-green-800 font-medium">Order ID:</p>
                            <p class="text-sm font-bold text-green-900">{{ $paymentOrderId }}</p>
                        </div>

                        @if($paymentInstructions['type'] === 'virtual_account')
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <p class="text-xs text-blue-800 font-medium mb-2">Nomor Virtual Account ({{ strtoupper($paymentInstructions['bank']) }}):</p>
                                <div class="flex items-center gap-2" x-data="{ copied: false }">
                                    <p class="text-2xl font-bold text-blue-900">{{ $paymentInstructions['va_number'] }}</p>
                                    <button @click="navigator.clipboard.writeText('{{ $paymentInstructions['va_number'] }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            :class="copied ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700'"
                                            class="px-3 py-1.5 text-white text-xs rounded transition-all duration-200 flex items-center gap-1.5 min-w-[70px] justify-center">
                                        <template x-if="!copied">
                                            <span>Copy</span>
                                        </template>
                                        <template x-if="copied">
                                            <div class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span>Copied!</span>
                                            </div>
                                        </template>
                                    </button>
                                </div>
                            </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-xs text-yellow-800 font-medium mb-2">Cara Pembayaran:</p>
                                <ol class="list-decimal list-inside space-y-1 text-xs text-yellow-800">
                                    @foreach($paymentInstructions['instructions'] as $instruction)
                                        <li>{{ $instruction }}</li>
                                    @endforeach
                                </ol>
                            </div>

                        @elseif($paymentInstructions['type'] === 'mandiri_echannel')
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3" x-data="{ copied: false }">
                                    <p class="text-xs text-blue-800 font-medium mb-1">Bill Key:</p>
                                    <div class="flex items-center gap-2">
                                        <p class="text-lg font-bold text-blue-900">{{ $paymentInstructions['bill_key'] }}</p>
                                        <button @click="navigator.clipboard.writeText('{{ $paymentInstructions['bill_key'] }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                :class="copied ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700'"
                                                class="px-2 py-1 text-white text-xs rounded transition-all duration-200 flex items-center gap-1 min-w-[60px] justify-center">
                                            <template x-if="!copied">
                                                <span>Copy</span>
                                            </template>
                                            <template x-if="copied">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </template>
                                        </button>
                                    </div>
                                </div>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3" x-data="{ copied: false }">
                                    <p class="text-xs text-blue-800 font-medium mb-1">Biller Code:</p>
                                    <div class="flex items-center gap-2">
                                        <p class="text-lg font-bold text-blue-900">{{ $paymentInstructions['biller_code'] }}</p>
                                        <button @click="navigator.clipboard.writeText('{{ $paymentInstructions['biller_code'] }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                :class="copied ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700'"
                                                class="px-2 py-1 text-white text-xs rounded transition-all duration-200 flex items-center gap-1 min-w-[60px] justify-center">
                                            <template x-if="!copied">
                                                <span>Copy</span>
                                            </template>
                                            <template x-if="copied">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </template>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-xs text-yellow-800 font-medium mb-2">Cara Pembayaran:</p>
                                <ol class="list-decimal list-inside space-y-1 text-xs text-yellow-800">
                                    @foreach($paymentInstructions['instructions'] as $instruction)
                                        <li>{{ $instruction }}</li>
                                    @endforeach
                                </ol>
                            </div>

                        @elseif($paymentInstructions['type'] === 'qris')
                            <div class="bg-white border-2 border-gray-200 rounded-lg p-6 mb-4 text-center">
                                @if(isset($paymentInstructions['qr_string']) && $qrCodeImage)
                                    <div class="mb-3">
                                        <img src="{{ $qrCodeImage }}" alt="QR Code" class="mx-auto" style="max-width: 250px; height: auto;">
                                    </div>
                                    <p class="text-xs text-gray-600">Scan QR code ini dengan aplikasi e-wallet atau mobile banking Anda</p>
                                    @if(isset($paymentInstructions['expiry_time']))
                                        <p class="text-xs text-red-600 mt-2 font-medium">Berlaku hingga: {{ $paymentInstructions['expiry_time'] }}</p>
                                    @endif
                                @elseif(isset($paymentInstructions['qr_string']))
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                        <p class="text-sm text-yellow-800 font-medium mb-2">QR Code sedang dimuat...</p>
                                        <p class="text-xs text-yellow-700">Jika QR code tidak muncul dalam beberapa detik, silakan refresh halaman atau gunakan metode pembayaran lain.</p>
                                    </div>
                                @else
                                    <div class="bg-gray-100 p-8 rounded-lg">
                                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M3 3h6v6H3V3zm2 2v2h2V5H5zm8-2h6v6h-6V3zm2 2v2h2V5h-2zM3 13h6v6H3v-6zm2 2v2h2v-2H5zm13-2h3v2h-3v-2zm-3 0h2v2h-2v-2zm3 3h3v3h-3v-3zm-3 3h2v2h-2v-2zm-3-3h2v2h-2v-2z"/>
                                        </svg>
                                        <p class="text-sm text-gray-600 font-medium">QR Code tidak tersedia</p>
                                        <p class="text-xs text-gray-500 mt-1">Silakan gunakan Virtual Account atau E-Wallet</p>
                                    </div>
                                @endif
                            </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-xs text-yellow-800 font-medium mb-2">Cara Pembayaran:</p>
                                <ol class="list-decimal list-inside space-y-1 text-xs text-yellow-800">
                                    @foreach($paymentInstructions['instructions'] as $instruction)
                                        <li>{{ $instruction }}</li>
                                    @endforeach
                                </ol>
                            </div>

                        @elseif($paymentInstructions['type'] === 'ewallet')
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <p class="text-sm text-blue-900 font-medium">
                                    Pembayaran akan diproses melalui {{ strtoupper($paymentInstructions['provider']) }}
                                </p>
                                <p class="text-xs text-blue-700 mt-1">Silakan cek notifikasi di aplikasi Anda untuk menyelesaikan pembayaran</p>
                            </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-xs text-yellow-800 font-medium mb-2">Cara Pembayaran:</p>
                                <ol class="list-decimal list-inside space-y-1 text-xs text-yellow-800">
                                    @foreach($paymentInstructions['instructions'] as $instruction)
                                        <li>{{ $instruction }}</li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                        <p class="text-xs text-gray-700">
                            <strong>Catatan:</strong> Status membership akan otomatis aktif setelah pembayaran berhasil diverifikasi oleh sistem. Anda tidak perlu upload bukti transfer.
                        </p>
                    </div>
                @else
                    <!-- Manual Bank Transfer (Fallback) -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <p class="text-xs text-gray-800 font-medium mb-2">Informasi Pembayaran:</p>
                        <div class="space-y-1 text-xs text-gray-800">
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
                            <p class="text-xs text-gray-800">
                                <strong>Pastikan:</strong> Bukti transfer jelas menampilkan nomor referensi dan nominal Rp {{ number_format(global_config('premium_membership_price', 100000), 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="button"
                                    wire:click="cancelUpload"
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition-colors cursor-pointer"
                                    {{ $isUploading ? 'disabled' : '' }}>
                                Batal
                            </button>
                            <button type="submit"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    {{ $isUploading ? 'disabled' : '' }}>
                                @if($isUploading)
                                    Uploading...
                                @else
                                    Upload
                                @endif
                            </button>
                        </div>
                    </form>
                @endif

                @if($paymentInstructions)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <button type="button"
                                @click="window.location.reload()"
                                class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded-lg transition-colors cursor-pointer">
                            Tutup
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Membership History Section -->
    @if($membershipHistory && $membershipHistory->count() > 0)
        <div class="mt-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Riwayat Premium Member
            </h2>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Harga
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Metode
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Periode
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($membershipHistory as $membership)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $membership->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Rp {{ number_format($membership->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ Str::upper($membership->payment_channel ?? 'Transfer Bank') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $badgeClasses = match($membership->status) {
                                                'active' => 'bg-green-100 text-green-800',
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'expired' => 'bg-gray-100 text-gray-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                            $statusLabels = [
                                                'active' => 'Aktif',
                                                'pending' => 'Pending',
                                                'expired' => 'Expired',
                                                'cancelled' => 'Dibatalkan',
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                            {{ $statusLabels[$membership->status] ?? ucfirst($membership->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        @if($membership->started_at && $membership->expires_at)
                                            <div class="text-xs">
                                                <div>{{ $membership->started_at->format('d M Y') }}</div>
                                                <div class="text-gray-500">s/d {{ $membership->expires_at->format('d M Y') }}</div>
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button wire:click="showMembershipDetail({{ $membership->id }})"
                                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-1.5 px-3 rounded text-xs transition-colors cursor-pointer">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden divide-y divide-gray-200">
                    @foreach($membershipHistory as $membership)
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $membership->created_at->format('d M Y') }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Rp {{ number_format($membership->price, 0, ',', '.') }}
                                    </p>
                                </div>
                                @php
                                    $badgeClasses = match($membership->status) {
                                        'active' => 'bg-green-100 text-green-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'expired' => 'bg-gray-100 text-gray-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                    $statusLabels = [
                                        'active' => 'Aktif',
                                        'pending' => 'Pending',
                                        'expired' => 'Expired',
                                        'cancelled' => 'Dibatalkan',
                                    ];
                                @endphp
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                    {{ $statusLabels[$membership->status] ?? ucfirst($membership->status) }}
                                </span>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-xs text-gray-700">
                                    <span>{{ $membership->payment_channel ?? 'Transfer Bank' }}</span>
                                </div>
                                
                                @if($membership->started_at && $membership->expires_at)
                                    <div class="text-xs text-gray-600">
                                        <span class="font-medium">Periode:</span><br>
                                        {{ $membership->started_at->format('d M Y') }} - {{ $membership->expires_at->format('d M Y') }}
                                    </div>
                                @endif
                                
                                <button wire:click="showMembershipDetail({{ $membership->id }})"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-1.5 px-3 rounded text-xs transition-colors cursor-pointer mt-2">
                                    Detail
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Membership Detail Modal -->
    @if($showDetailModal && $selectedMembership)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:click="closeDetailModal">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto" wire:click.stop>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">Detail Membership</h2>
                        <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Membership Info -->
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Order ID</span>
                            <span class="text-sm font-medium text-gray-900">{{ $selectedMembership->transaction_id ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Tanggal Pembelian</span>
                            <span class="text-sm font-medium text-gray-900">{{ $selectedMembership->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Total Pembayaran</span>
                            <span class="text-lg font-bold text-green-600">Rp {{ number_format($selectedMembership->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Metode Pembayaran</span>
                            <span class="text-sm font-medium text-gray-900 uppercase">{{ $selectedMembership->payment_channel ?? 'Transfer Bank' }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Status</span>
                            @php
                                $badgeClasses = match($selectedMembership->status) {
                                    'active' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'expired' => 'bg-gray-100 text-gray-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                                $statusLabels = [
                                    'active' => 'Aktif',
                                    'pending' => 'Pending',
                                    'expired' => 'Expired',
                                    'cancelled' => 'Dibatalkan',
                                ];
                            @endphp
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                {{ $statusLabels[$selectedMembership->status] ?? ucfirst($selectedMembership->status) }}
                            </span>
                        </div>
                        @if($selectedMembership->started_at && $selectedMembership->expires_at)
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                <span class="text-sm text-gray-600">Periode Aktif</span>
                                <span class="text-sm font-medium text-gray-900">{{ $selectedMembership->started_at->format('d M Y') }} s/d {{ $selectedMembership->expires_at->format('d M Y') }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Payment Instructions (for pending status) -->
                    @if($selectedMembership->status === 'pending')
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <h3 class="font-700 text-md text-gray-800 mb-3 flex">
                                Informasi Pembayaran
                            </h3>

                            @php
                                $midtransResponse = $selectedMembership->midtrans_response ?? [];
                            @endphp
                            @if($selectedMembership->payment_method === 'midtrans' && !empty($midtransResponse))
                                {{-- QRIS Payment --}}
                                @if($selectedMembership->payment_type === 'qris' || ($midtransResponse['payment_type'] ?? '') === 'qris')
                                    <div class="mb-4">
                                        <p class="text-sm font-medium text-blue-800 mb-2">Scan QR Code:</p>
                                        @php
                                            // Try multiple possible QR string locations in Midtrans response
                                            $qrString = $midtransResponse['qr_string']
                                                ?? ($midtransResponse['actions'][0]['url'] ?? null)
                                                ?? ($midtransResponse['actions'][1]['url'] ?? null)
                                                ?? $midtransResponse['payment_code']
                                                ?? $midtransResponse['qr_code_url']
                                                ?? null;
                                        @endphp
                                        @if($qrString)
                                            <div class="">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrString) }}" alt="QRIS Code" class="w-48 h-48">
                                            </div>
                                            <p class="text-xs text-gray-600 mt-2">Scan dengan aplikasi e-wallet atau mobile banking Anda</p>
                                        @else
                                            <div class="bg-yellow-50 border border-yellow-200 rounded p-3 text-xs text-yellow-700">
                                                QR Code tidak tersedia. Silakan cek email Anda atau hubungi admin.
                                            </div>
                                        @endif
                                    </div>
                                {{-- Bank Transfer / Virtual Account --}}
                                @elseif(isset($midtransResponse['va_numbers']) && is_array($midtransResponse['va_numbers']))
                                    <div class="space-y-3">
                                        @foreach($midtransResponse['va_numbers'] as $va)
                                            <div class="">
                                                <p class="text-sm font-bold text-gray-900 uppercase">{{ $va['bank'] ?? 'Bank Transfer' }}</p>
                                                <p class="text-[12px] text-gray-600 mt-1">No. Virtual Account:</p>
                                                <p class="text-md font-bold text-blue-700">{{ $va['va_number'] ?? '-' }}</p>
                                                <button type="button"
                                                        x-data="{ copied: false }"
                                                        @click="navigator.clipboard.writeText('{{ $va['va_number'] ?? '-' }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                        class="inline-flex items-center justify-center w-6 h-6 rounded bg-gray-600 hover:bg-gray-700 text-white transition-colors cursor-pointer"
                                                        title="Copy">
                                                    <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                    <svg x-show="copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                {{-- Bill Payment / Mandiri --}}
                                @elseif(isset($midtransResponse['biller_code']) && isset($midtransResponse['bill_key']))
                                    <div class="">
                                        <p class="text-sm font-bold text-gray-900">Mandiri Bill Payment</p>
                                        <p class="text-sm text-gray-600 mt-1">Kode Perusahaan:</p>
                                        <p class="text-lg font-bold text-blue-700">{{ $midtransResponse['biller_code'] }}</p>
                                        <p class="text-sm text-gray-600 mt-2">Kode Pembayaran:</p>
                                        <p class="text-lg font-bold text-blue-700">{{ $midtransResponse['bill_key'] }}</p>
                                    </div>
                                {{-- Convenience Store --}}
                                @elseif(isset($midtransResponse['payment_code']) && ($midtransResponse['store'] ?? $midtransResponse['payment_type'] ?? '') === 'cstore')
                                    <div class="">
                                        <p class="text-sm font-bold text-gray-900">{{ $midtransResponse['store'] ?? 'Convenience Store' }}</p>
                                        <p class="text-sm text-gray-600 mt-1">Kode Pembayaran:</p>
                                        <p class="text-lg font-bold text-blue-700">{{ $midtransResponse['payment_code'] }}</p>
                                    </div>
                                @else
                                    <p class="text-sm text-blue-800">Silakan selesaikan pembayaran sesuai instruksi yang dikirim ke email Anda.</p>
                                    @if(isset($midtransResponse['redirect_url']))
                                        <a href="{{ $midtransResponse['redirect_url'] }}" target="_blank" class="text-sm text-blue-600 underline mt-2 inline-block">Klik di sini untuk pembayaran</a>
                                    @endif
                                @endif

                                @if($selectedMembership->transaction_id)
                                    <div class="mt-3 pt-3 border-t border-blue-200">
                                        <p class="text-xs text-gray-600">Transaction ID: {{ $selectedMembership->transaction_id }}</p>
                                    </div>
                                @endif
                            @elseif($selectedMembership->payment_method === 'bank_transfer')
                                <div class="space-y-3">
                                    <p class="text-sm text-blue-800">Silakan transfer ke rekening berikut:</p>
                                    <div class="">
                                        @php
                                            $bankAccounts = \App\Models\GlobalConfig::getBankAccounts();
                                        @endphp
                                        @if(!empty($bankAccounts))
                                            @foreach($bankAccounts as $account)
                                                <div class="mb-2 pb-2 border-b border-gray-100 last:border-0 last:mb-0 last:pb-0">
                                                    <p class="text-sm font-bold text-gray-900">{{ $account['bank_name'] ?? 'Bank' }}</p>
                                                    <p class="text-sm text-gray-600">No. Rekening: <span class="font-medium text-gray-900">{{ $account['account_number'] ?? '-' }}</span></p>
                                                    <p class="text-sm text-gray-600">Atas Nama: <span class="font-medium text-gray-900">{{ $account['account_name'] ?? '-' }}</span></p>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-sm text-gray-600">Silakan hubungi admin untuk informasi rekening transfer.</p>
                                        @endif
                                    </div>
                                    @if($selectedMembership->payment_proof_path)
                                        <div class="bg-green-100 border border-green-200 rounded-lg p-3 mt-3">
                                            <p class="text-sm text-green-800 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Bukti transfer sudah diupload
                                            </p>
                                        </div>
                                    @else
                                        <p class="text-sm text-yellow-700 mt-2">Belum upload bukti transfer</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    <button wire:click="closeDetailModal"
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded-lg transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
