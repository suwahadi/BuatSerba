<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">💎 Premium Membership</h1>
        <p class="text-gray-600">Dapatkan keuntungan eksklusif dengan menjadi member premium kami.</p>
    </div>

    <!-- Current Membership Status (if exists) -->
    @if($activeMembership)
        <div class="mb-8 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg shadow-sm border border-purple-200 p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-sm font-bold text-purple-900">🌟 MEMBERSHIP AKTIF</span>
                    </div>
                    <h3 class="text-lg font-bold text-purple-900">Anda adalah member premium</h3>
                </div>
                <div class="w-12 h-12 bg-purple-200 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg p-3">
                    <p class="text-xs text-gray-600 font-medium">Status</p>
                    <p class="text-sm font-bold text-purple-900 mt-1">
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Aktif</span>
                    </p>
                </div>
                <div class="bg-white rounded-lg p-3">
                    <p class="text-xs text-gray-600 font-medium">Berakhir</p>
                    <p class="text-sm font-bold text-purple-900 mt-1">
                        {{ $activeMembership->expires_at ? $activeMembership->expires_at->format('d M Y') : '-' }}
                    </p>
                </div>
                <div class="bg-white rounded-lg p-3">
                    <p class="text-xs text-gray-600 font-medium">Sisa Waktu</p>
                    <p class="text-sm font-bold text-purple-900 mt-1">
                        @if($daysRemaining)
                            {{ $daysRemaining }} hari
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex gap-3">
                <button wire:click="renew"
                        class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-600">
                    🔄 Perpanjang Membership
                </button>
            </div>
        </div>
    @elseif($pendingMembership)
        <div class="mb-8 bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg shadow-sm border border-yellow-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-bold text-yellow-900">⏳ Menunggu Verifikasi</h3>
                    <p class="text-sm text-yellow-800 mt-1">Silakan upload bukti transfer untuk mengaktifkan membership.</p>
                </div>
                <button wire:click="$set('showUploadModal', true)"
                        class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition-all duration-200">
                    📤 Upload Bukti
                </button>
            </div>
        </div>
    @else
        <!-- Benefit Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Benefits Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">✨ Keuntungan Premium</h3>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <span class="text-green-600 font-bold text-lg flex-shrink-0">✓</span>
                        <div>
                            <p class="text-sm font-bold text-gray-900">1% Cashback Instant</p>
                            <p class="text-xs text-gray-600">Setiap pembelian langsung dapat cashback</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-green-600 font-bold text-lg flex-shrink-0">✓</span>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Berlaku 1 Tahun Penuh</p>
                            <p class="text-xs text-gray-600">Keuntungan berlaku sepanjang tahun</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-green-600 font-bold text-lg flex-shrink-0">✓</span>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Untuk Semua Produk</p>
                            <p class="text-xs text-gray-600">Cashback berlaku di semua kategori</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-green-600 font-bold text-lg flex-shrink-0">✓</span>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Akses Eksklusif</p>
                            <p class="text-xs text-gray-600">Penawaran spesial hanya untuk member</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-green-600 font-bold text-lg flex-shrink-0">✓</span>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Priority Support</p>
                            <p class="text-xs text-gray-600">Layanan customer service prioritas</p>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Pricing Card -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-sm border border-purple-200 p-6">
                <div class="mb-6">
                    <p class="text-xs text-purple-600 font-bold uppercase tracking-wide">Paket Membership</p>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-4xl font-bold text-purple-900">
                            Rp {{ number_format($price, 0, ',', '.') }}
                        </span>
                        <span class="text-sm text-purple-600 font-medium">/Tahun</span>
                    </div>
                </div>

                <div class="space-y-3 mb-6 py-6 border-t border-b border-purple-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-purple-900">Durasi</span>
                        <span class="font-bold text-purple-900">12 Bulan</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-purple-900">Cashback</span>
                        <span class="font-bold text-purple-900">1% / Pembelian</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-purple-900">Mulai Berlaku</span>
                        <span class="font-bold text-purple-900">{{ now()->format('d M Y') }}</span>
                    </div>
                </div>

                <button wire:click="openPurchaseModal"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-600">
                    <span class="text-lg">💳</span> Beli Premium Sekarang
                </button>

                <p class="text-xs text-purple-700 text-center mt-4 leading-relaxed">
                    Pembayaran via transfer bank. Verifikasi manual oleh admin.
                </p>
            </div>
        </div>
    @endif

    <!-- Purchase Confirmation Modal -->
    @if($showPurchaseModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 animate-in">
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-gray-900">💳 Konfirmasi Pembelian</h3>
                </div>

                <div class="space-y-4 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 font-medium">Harga</span>
                        <span class="font-bold text-gray-900">Rp {{ number_format($price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 font-medium">Durasi</span>
                        <span class="font-bold text-gray-900">1 Tahun</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 font-medium">Mulai</span>
                        <span class="font-bold text-gray-900">{{ now()->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-4 border-t border-gray-300">
                        <span class="text-gray-900 font-bold">Total</span>
                        <span class="text-2xl font-bold text-purple-600">Rp {{ number_format($price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex items-start gap-3 mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <input type="checkbox" wire:model="termsAccepted" id="terms" class="mt-1 rounded">
                    <label for="terms" class="text-sm text-gray-700">
                        Saya setuju dengan <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">syarat dan ketentuan</a> premium membership
                    </label>
                </div>

                <div class="flex gap-3">
                    <button wire:click="closePurchaseModal"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold py-2.5 px-4 rounded-lg transition-all duration-200">
                        Batal
                    </button>
                    <button wire:click="purchaseMembership"
                            class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-600">
                        Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Upload Proof Modal -->
    @if($showUploadModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 animate-in">
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-gray-900">📤 Upload Bukti Transfer</h3>
                </div>

                <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm font-bold text-gray-900 mb-3">Transfer ke:</p>
                    <div class="space-y-2 ml-2">
                        <p class="text-sm text-gray-700"><span class="font-medium">Bank:</span> BCA</p>
                        <p class="text-sm text-gray-700"><span class="font-medium">No Rek:</span> 123456789</p>
                        <p class="text-sm text-gray-700"><span class="font-medium">A/N:</span> PT Buat Serba</p>
                    </div>
                </div>

                <!-- File Upload -->
                <div class="mb-6">
                    <div class="relative">
                        <label class="block">
                            <div class="cursor-pointer px-4 py-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-400 bg-gray-50 hover:bg-purple-50 transition-all">
                                <input type="file" wire:model="proofFile" accept="image/*" class="sr-only">
                                <div class="text-center">
                                    @if($proofFile)
                                        <svg class="w-12 h-12 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-sm font-bold text-gray-900">{{ $proofFile->getClientOriginalName() }}</p>
                                        <p class="text-xs text-gray-600">{{ number_format($proofFile->getSize() / 1024, 0) }} KB</p>
                                    @else
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <p class="text-sm font-bold text-gray-900">Klik atau drag file ke sini</p>
                                        <p class="text-xs text-gray-600">Format: JPG, PNG | Max: 5MB</p>
                                    @endif
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('proofFile')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                @if($uploadError)
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-700">❌ {{ $uploadError }}</p>
                    </div>
                @endif

                <div class="flex gap-3">
                    <button wire:click="closeUploadModal"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold py-2.5 px-4 rounded-lg transition-all duration-200">
                        Batal
                    </button>
                    <button wire:click="uploadProof" 
                            wire:loading.attr="disabled"
                            class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove>📤 Upload</span>
                        <span wire:loading>⏳ Mengupload...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Success Modal -->
    @if($showSuccessModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 text-center animate-in">
                <div class="mb-4">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">✅ Berhasil!</h3>
                <p class="text-gray-600 mb-6">Bukti transfer berhasil diupload. Admin akan segera memverifikasi pembelian Anda.</p>
                <p class="text-sm text-gray-500 mb-6">Silahkan cek status membership Anda di halaman "Membership Saya"</p>

                <button wire:click="closeSuccessModal"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition-all duration-200">
                    Lihat Status Membership
                </button>
            </div>
        </div>
    @endif
</div>
