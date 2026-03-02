<div x-data="{ showToast: false, toastMessage: '' }">
    <div class="max-w-4xl mx-auto px-3 sm:px-4 py-4 sm:py-8">
        <div class="bg-white rounded-lg sm:rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-3 sm:px-6 py-4 sm:py-6 text-white">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-sm sm:text-lg md:text-xl font-bold truncate">Pembayaran Pesanan</h1>
                        <div class="flex items-center mt-1 sm:mt-2 space-x-1 sm:space-x-2">
                            <p class="text-xs sm:text-sm opacity-90 truncate">Order ID: #{{ $order->order_number }}</p>
                            <button @click="
                                navigator.clipboard.writeText('{{ $order->order_number }}');
                                showToast = true;
                                toastMessage = 'Order ID berhasil disalin!';
                                setTimeout(() => showToast = false, 3000);
                            " class="p-0.5 sm:p-1 hover:bg-green-500 rounded transition-colors flex-shrink-0" title="Salin Order ID">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mt-0.5 {{ $order->getPaymentStatusBadgeClasses() }}">
                            {{ $order->getPaymentStatusShortLabel() }}
                        </span>
                    </div>
                    <div class="flex-shrink-0">
                        {{-- Mobile: Label first, left-aligned --}}
                        <p class="sm:hidden text-xs opacity-90 text-left">Total Pembayaran</p>
                        <p class="text-base sm:text-xl md:text-2xl font-bold text-left sm:text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                        {{-- Desktop: Label after, right-aligned --}}
                        <p class="hidden sm:block mt-0.5 sm:mt-1 text-xs opacity-90 text-right">Total Pembayaran</p>
                    </div>
                </div>
            </div>

            {{-- Payment Expiration Countdown --}}
            @if(isset($paymentData['expired_at']) && $order->payment_status === 'pending')
            <div class="p-3 sm:p-4 md:p-6 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50"
                 x-data="{
                    expiredAt: '{{ $paymentData['expired_at'] }}',
                    days: 0,
                    hours: 0,
                    minutes: 0,
                    seconds: 0,
                    isExpired: false,
                    isUrgent: false,
                    interval: null,
                    init() {
                        this.updateCountdown();
                        this.interval = setInterval(() => this.updateCountdown(), 1000);
                    },
                    updateCountdown() {
                        const now = new Date().getTime();
                        const expiry = new Date(this.expiredAt).getTime();
                        const distance = expiry - now;

                        if (distance <= 0) {
                            this.isExpired = true;
                            this.days = 0;
                            this.hours = 0;
                            this.minutes = 0;
                            this.seconds = 0;
                            clearInterval(this.interval);
                            return;
                        }

                        this.days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        this.hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        this.seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        
                        // Mark as urgent if less than 1 hour remaining
                        this.isUrgent = distance < (1000 * 60 * 60);
                    },
                    formatNumber(num) {
                        return num.toString().padStart(2, '0');
                    }
                 }">
                <div class="text-center">
                    <p class="text-xs sm:text-sm font-medium text-gray-700 mb-2 sm:mb-3">
                        <template x-if="!isExpired">
                            <span>Selesaikan pembayaran sebelum</span>
                        </template>
                        <template x-if="isExpired">
                            <span class="text-red-600">Waktu pembayaran telah berakhir</span>
                        </template>
                    </p>
                    
                    {{-- Digital Countdown Display --}}
                    <div class="flex items-center justify-center gap-1.5 sm:gap-2 md:gap-3" x-show="!isExpired">
                        {{-- Days (only show if > 0) --}}
                        <template x-if="days > 0">
                            <div class="flex items-center gap-1.5 sm:gap-2">
                                <div class="flex flex-col items-center">
                                    <div class="bg-gradient-to-b from-gray-800 to-gray-900 text-white font-mono font-bold text-lg sm:text-2xl md:text-3xl px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 md:py-3 rounded-lg shadow-lg min-w-[40px] sm:min-w-[50px] md:min-w-[60px]"
                                         :class="{ 'from-red-600 to-red-800': isUrgent }">
                                        <span x-text="formatNumber(days)"></span>
                                    </div>
                                    <span class="text-[10px] sm:text-xs text-gray-500 mt-1">Hari</span>
                                </div>
                                <span class="text-gray-400 font-bold text-lg sm:text-xl md:text-2xl pb-4">:</span>
                            </div>
                        </template>
                        
                        {{-- Hours --}}
                        <div class="flex flex-col items-center">
                            <div class="bg-gradient-to-b from-gray-800 to-gray-900 text-white font-mono font-bold text-lg sm:text-2xl md:text-3xl px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 md:py-3 rounded-lg shadow-lg min-w-[40px] sm:min-w-[50px] md:min-w-[60px]"
                                 :class="{ 'from-red-600 to-red-800': isUrgent }">
                                <span x-text="formatNumber(hours)"></span>
                            </div>
                            <span class="text-[10px] sm:text-xs text-gray-500 mt-1">Jam</span>
                        </div>
                        
                        <span class="text-gray-400 font-bold text-lg sm:text-xl md:text-2xl pb-4" :class="{ 'text-red-500': isUrgent }">:</span>
                        
                        {{-- Minutes --}}
                        <div class="flex flex-col items-center">
                            <div class="bg-gradient-to-b from-gray-800 to-gray-900 text-white font-mono font-bold text-lg sm:text-2xl md:text-3xl px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 md:py-3 rounded-lg shadow-lg min-w-[40px] sm:min-w-[50px] md:min-w-[60px]"
                                 :class="{ 'from-red-600 to-red-800': isUrgent }">
                                <span x-text="formatNumber(minutes)"></span>
                            </div>
                            <span class="text-[10px] sm:text-xs text-gray-500 mt-1">Menit</span>
                        </div>
                        
                        <span class="text-gray-400 font-bold text-lg sm:text-xl md:text-2xl pb-4" :class="{ 'text-red-500': isUrgent, 'animate-pulse': true }">:</span>
                        
                        {{-- Seconds --}}
                        <div class="flex flex-col items-center">
                            <div class="bg-gradient-to-b from-gray-800 to-gray-900 text-white font-mono font-bold text-lg sm:text-2xl md:text-3xl px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 md:py-3 rounded-lg shadow-lg min-w-[40px] sm:min-w-[50px] md:min-w-[60px]"
                                 :class="{ 'from-red-600 to-red-800': isUrgent }">
                                <span x-text="formatNumber(seconds)"></span>
                            </div>
                            <span class="text-[10px] sm:text-xs text-gray-500 mt-1">Detik</span>
                        </div>
                    </div>

                    {{-- Expired State --}}
                    <div x-show="isExpired" class="flex items-center justify-center gap-2 py-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-red-600 font-semibold text-sm sm:text-base">Pembayaran Kedaluwarsa</span>
                    </div>

                    {{-- Warning message when urgent --}}
                    <div x-show="isUrgent && !isExpired" class="mt-2 sm:mt-3">
                        <p class="text-xs sm:text-sm text-red-600 font-medium animate-pulse">
                            Segera selesaikan pembayaran Anda!
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Payment Instructions -->
            {{-- Success Message for All Payment Methods --}}
            @if($order->payment_status === 'paid')
            <div class="p-4 sm:p-6 md:p-8 border-b border-gray-100 bg-gradient-to-br from-green-50 to-emerald-50">
                <div class="flex flex-col items-center text-center">
                    <div class="mb-4 flex items-center justify-center">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-green-500 rounded-full flex items-center justify-center animate-pulse">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-green-800 mb-2">Pembayaran Berhasil! 🎉</h2>
                    <p class="text-sm sm:text-base text-green-700 leading-relaxed max-w-md mb-4">
                        @if($order->payment_method === 'member_balance')
                            Pembayaran melalui saldo member Anda telah berhasil diproses.
                        @elseif($order->payment_method === 'cash')
                            Pembayaran tunai Anda telah berhasil dicatat.
                        @else
                            Pembayaran Anda telah berhasil dikonfirmasi dan diproses.
                        @endif
                    </p>
                    <div class="bg-white rounded-lg p-4 sm:p-6 border-2 border-green-200 w-full max-w-sm">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Metode Pembayaran:</span>
                                <span class="font-semibold text-gray-900">{{ $order->getPaymentMethodLabel() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Total Pembayaran:</span>
                                <span class="font-bold text-lg text-green-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm">Status:</span>
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">{{ $order->getPaymentStatusShortLabel() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @elseif(in_array($order->payment_method, ['member_balance', 'cash']) && $order->payment_status === 'pending')
            <div class="p-4 sm:p-6 md:p-8 border-b border-gray-100 bg-gradient-to-br from-blue-50 to-sky-50">
                <div class="flex flex-col items-center text-center">
                    <div class="mb-4">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto animate-spin">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-lg sm:text-xl font-bold text-blue-800 mb-2">Pembayaran Sedang Diproses</h2>
                    <p class="text-sm sm:text-base text-blue-700 leading-relaxed max-w-md">
                        @if($order->payment_method === 'member_balance')
                            Pembayaran melalui saldo member sedang diproses. Pesanan Anda akan segera dikonfirmasi.
                        @else
                            Pembayaran tunai sedang diproses. Pesanan Anda akan segera dikonfirmasi.
                        @endif
                    </p>
                </div>
            </div>
            {{-- Expired Payment Section --}}
            @elseif($order->payment_status === 'expired')
            <div class="p-4 sm:p-6 md:p-8 border-b border-gray-100 bg-gradient-to-br from-red-50 to-orange-50">
                <div class="flex flex-col items-center text-center">
                    <div class="mb-4">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-red-500 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-xl sm:text-xl font-bold text-red-800 mb-3">Pembayaran Kedaluwarsa</h2>
                    <p class="text-sm sm:text-base text-red-700 leading-relaxed mb-6 max-w-xl">
                        Waktu pembayaran untuk pesanan ini telah berakhir. Untuk melanjutkan, Anda perlu membuat pesanan baru dengan metode pembayaran yang berbeda.
                    </p>
                    <div class="bg-white rounded-lg p-4 sm:p-6 border-2 border-red-200 w-full max-w-sm mb-6">
                        <div class="space-y-4">
                            <div class="text-left">
                                <h4 class="font-semibold text-gray-900 text-sm mb-2">Mengapa Pesanan Kadaluarsa?</h4>
                                <ul class="text-xs sm:text-sm text-gray-700 space-y-1 list-disc list-inside">
                                    <li>Pembayaran tidak diselesaikan dalam waktu yang diberikan</li>
                                    <li>Metode pembayaran yang dipilih mungkin sedang gangguan atau tidak tersedia</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 mb-4">
                        Jika Anda mengalami kesulitan, silakan hubungi tim customer service kami untuk bantuan lebih lanjut.
                    </p>
                </div>
            </div>
            @elseif($order->payment_method === 'transfer' && $order->payment_status !== 'expired')
            <div class="p-3 sm:p-4 md:p-6 border-b border-gray-100 bg-white">
                <h3 class="text-xs sm:text-sm font-semibold text-gray-800 mb-2 sm:mb-3">Cara Pembayaran</h3>
                
                <div class="bg-white rounded-lg p-3 sm:p-4 mb-3 sm:mb-4 border-2 border-green-200">
                    <div class="flex items-center justify-between mb-2 sm:mb-3">
                        <span class="text-xs sm:text-sm font-medium text-gray-900">Bank Transfer {{ global_config('manual_bank_name') ?? 'BCA' }}</span>
                        <button @click="
                            navigator.clipboard.writeText('{{ global_config('manual_bank_account_number') }}');
                            showToast = true;
                            toastMessage = 'Nomor Rekening berhasil disalin!';
                            setTimeout(() => showToast = false, 3000);
                        " class="flex items-center space-x-1 px-2 sm:px-3 py-1 sm:py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-xs font-medium">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span>Salin</span>
                        </button>
                    </div>
                    <div class="text-base sm:text-lg md:text-xl font-mono font-bold text-center py-3 sm:py-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200 overflow-x-auto">
                        <div class="min-w-0 px-2">{{ global_config('manual_bank_account_number') }}</div>
                    </div>
                    <p class="text-xs text-gray-600 mt-2 sm:mt-3 text-center font-medium">
                        a.n {{ global_config('manual_bank_account_name') }}
                    </p>
                    <p class="text-xs text-gray-600 mt-2 text-center leading-relaxed">
                        Silakan transfer ke rekening di atas dan lakukan konfirmasi pembayaran Anda.
                    </p>
                </div>

                <ol class="list-decimal list-inside space-y-1.5 sm:space-y-2">
                    <li class="text-xs sm:text-sm text-gray-700">Lakukan transfer sesuai total tagihan ke rekening di atas.</li>
                    <li class="text-xs sm:text-sm text-gray-700">Simpan bukti transfer Anda.</li>
                    <li class="text-xs sm:text-sm text-gray-700">Klik tombol <strong>Konfirmasi Pembayaran</strong> di bawah ini.</li>
                </ol>
            </div>
            @elseif(isset($paymentInstructions) && !empty($paymentInstructions) && $order->payment_status === 'pending' && $order->status !== 'cancelled' && !in_array($order->payment_method, ['member_balance', 'cash']) && (!isset($paymentData['transaction_status']) || !in_array($paymentData['transaction_status'], ['expire', 'expired'])))
            <div class="p-3 sm:p-4 md:p-6 border-b border-gray-100 bg-white">
                <h3 class="text-xs sm:text-sm font-semibold text-gray-800 mb-2 sm:mb-3">Cara Pembayaran</h3>
                
                @if($paymentInstructions['type'] === 'virtual_account')
                <div class="bg-white rounded-lg p-3 sm:p-4 mb-3 sm:mb-4 border-2 border-green-200">
                    <div class="flex items-center justify-between mb-2 sm:mb-3">
                        <span class="text-xs sm:text-sm font-medium text-gray-900">{{ strtoupper($paymentInstructions['bank']) }} Virtual Account</span>
                        <button @click="
                            navigator.clipboard.writeText('{{ $paymentInstructions['va_number'] }}');
                            showToast = true;
                            toastMessage = 'Nomor VA berhasil disalin!';
                            setTimeout(() => showToast = false, 3000);
                        " class="flex items-center space-x-1 px-2 sm:px-3 py-1 sm:py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-xs font-medium">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span>Salin</span>
                        </button>
                    </div>
                    <div class="text-base sm:text-lg md:text-xl font-mono font-bold text-center py-3 sm:py-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200 overflow-x-auto">
                        <div class="min-w-0 px-2">{{ $paymentInstructions['va_number'] }}</div>
                    </div>
                    <p class="text-xs text-gray-600 mt-2 sm:mt-3 text-center leading-relaxed">
                        Gunakan nomor ini untuk melakukan pembayaran melalui ATM, mobile banking, atau internet banking
                    </p>
                </div>
                @endif

                @if($paymentInstructions['type'] === 'mandiri_echannel')
                <div class="bg-white rounded-lg p-3 sm:p-4 mb-3 sm:mb-4 border-2 border-green-200">
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <span class="text-xs sm:text-sm font-medium text-gray-900">Mandiri Bill Payment</span>
                    </div>
                    
                    {{-- Biller Code --}}
                    <div class="mb-3 sm:mb-4">
                        <div class="flex items-center justify-between mb-1.5 sm:mb-2">
                            <span class="text-xs text-gray-600">Kode Perusahaan (Biller Code)</span>
                            <button @click="
                                navigator.clipboard.writeText('{{ $paymentInstructions['biller_code'] }}');
                                showToast = true;
                                toastMessage = 'Kode Perusahaan berhasil disalin!';
                                setTimeout(() => showToast = false, 3000);
                            " class="flex items-center space-x-1 px-2 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-xs font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span>Salin</span>
                            </button>
                        </div>
                        <div class="text-lg sm:text-xl md:text-2xl font-mono font-bold text-center py-2 sm:py-3 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg border border-yellow-300">
                            {{ $paymentInstructions['biller_code'] }}
                        </div>
                    </div>

                    {{-- Bill Key --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5 sm:mb-2">
                            <span class="text-xs text-gray-600">Kode Pembayaran (Bill Key)</span>
                            <button @click="
                                navigator.clipboard.writeText('{{ $paymentInstructions['bill_key'] }}');
                                showToast = true;
                                toastMessage = 'Kode Pembayaran berhasil disalin!';
                                setTimeout(() => showToast = false, 3000);
                            " class="flex items-center space-x-1 px-2 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-xs font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span>Salin</span>
                            </button>
                        </div>
                        <div class="text-base sm:text-lg md:text-xl font-mono font-bold text-center py-3 sm:py-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200 overflow-x-auto">
                            <div class="min-w-0 px-2">{{ $paymentInstructions['bill_key'] }}</div>
                        </div>
                    </div>

                    <p class="text-xs text-gray-600 mt-3 sm:mt-4 text-center leading-relaxed">
                        Gunakan Kode Perusahaan dan Kode Pembayaran di atas untuk melakukan pembayaran melalui ATM Mandiri, Mandiri Online, atau Livin' by Mandiri
                    </p>
                </div>
                @endif

                @if($paymentInstructions['type'] === 'ewallet')
                <div class="bg-white rounded-lg p-3 sm:p-4 mb-3 sm:mb-4 border-2 border-green-200">
                    <div class="text-center">
                        <span class="text-sm sm:text-base font-medium">{{ strtoupper($paymentInstructions['provider']) }}</span>
                        <p class="text-xs sm:text-sm text-gray-600 mt-2">
                            Ikuti instruksi di aplikasi {{ ucfirst($paymentInstructions['provider']) }} untuk menyelesaikan pembayaran
                        </p>
                    </div>
                </div>
                @endif

                @if($paymentInstructions['type'] === 'qris')
                <div class="bg-white rounded-lg p-3 sm:p-4 mb-3 sm:mb-4 text-center border-2 border-green-200">
                    <p class="text-sm sm:text-base font-medium mb-2">Scan Kode QR</p>
                    <div class="border-2 border-green-300 rounded-lg p-2 inline-block">
                        <!-- In a real implementation, you would display the QR code here -->
                        <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-dashed border-green-400 rounded-xl w-36 h-36 sm:w-48 sm:h-48 flex items-center justify-center">
                            <span class="text-green-600 font-medium text-xs sm:text-sm">QR Code</span>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2">
                        Scan kode QR di atas menggunakan aplikasi pembayaran QRIS
                    </p>
                </div>
                @endif

                @if(isset($paymentInstructions['instructions']) && !empty($paymentInstructions['instructions']))
                <ol class="list-decimal list-inside space-y-1.5 sm:space-y-2">
                    @foreach($paymentInstructions['instructions'] as $instruction)
                    <li class="text-xs sm:text-sm text-gray-700">{{ $instruction }}</li>
                    @endforeach
                </ol>
                @endif
            </div>
            @endif

            <!-- Actions -->
            <div class="p-3 sm:p-4 md:p-6 flex flex-col sm:flex-row gap-2 sm:gap-3">
                <a href="{{ route('dashboard') }}" 
                   class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-xs sm:text-sm rounded-lg hover:bg-gray-50 text-center transition font-medium">
                    Kembali ke Dashboard
                </a>
                
                @if($order->payment_status === 'expired')
                <a href="{{ route('catalog') }}" 
                   class="flex-1 px-4 py-2 bg-green-600 text-white text-xs sm:text-sm rounded-lg hover:bg-green-700 text-center transition font-medium">
                    Order Lagi
                </a>
                @elseif($order->payment_method === 'transfer')
                <a href="{{ route('payment.confirmation', ['code' => $order->order_number]) }}" 
                   class="flex-1 px-4 py-2 bg-green-600 text-white text-xs sm:text-sm rounded-lg hover:bg-green-700 text-center transition font-medium">
                    Konfirmasi Pembayaran
                </a>
                @elseif($order->payment_status !== 'paid')
                <a href="{{ route('order.detail', $order->order_number) }}" 
                   class="flex-1 px-4 py-2 bg-green-600 text-white text-xs sm:text-sm rounded-lg hover:bg-green-700 text-center transition font-medium">
                    Cek Status Pembayaran
                </a>
                @else
                <a href="{{ route('order.detail', $order->order_number) }}" 
                   class="flex-1 px-4 py-2 bg-green-600 text-white text-xs sm:text-sm rounded-lg hover:bg-green-700 text-center transition font-medium">
                    Lihat Detail Pesanan
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed bottom-4 right-4 bg-green-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2 text-xs sm:text-sm"
         style="display: none;">
        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span x-text="toastMessage"></span>
    </div>
</div>