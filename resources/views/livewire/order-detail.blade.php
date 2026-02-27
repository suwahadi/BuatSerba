<div x-data="{ showToast: false, toastMessage: '' }">
    <div class="max-w-4xl mx-auto px-3 sm:px-4 py-4 sm:py-8">
        <div class="bg-white rounded-lg sm:rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-3 sm:px-6 py-4 sm:py-6 text-white">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-sm sm:text-lg md:text-xl font-bold truncate">Detail Pesanan</h1>
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
                            ⚠️ Segera selesaikan pembayaran Anda!
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Order Status -->
            <div class="p-3 sm:p-4 md:p-6 border-b border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4">
                    <div>
                        <h2 class="text-sm sm:text-base font-semibold text-gray-800 mb-1 sm:mb-2">Status Pesanan</h2>
                        <span class="inline-flex items-center px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-xs sm:text-sm font-medium {{ $order->getOrderStatusBadgeClasses() }}">
                            {{ $order->getOrderStatusLabel() }}
                        </span>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-xs sm:text-sm text-gray-600">Tanggal Pemesanan</p>
                        <p class="text-xs sm:text-sm font-medium">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="p-3 sm:p-4 md:p-6 border-b border-gray-100">
                <h2 class="text-sm sm:text-base font-semibold text-gray-800 mb-2 sm:mb-4">Item Pesanan</h2>
                <div class="space-y-2 sm:space-y-3">
                    @foreach($order->items as $item)
                    <div class="flex items-center gap-2 sm:gap-3 md:gap-4 p-2 sm:p-3 md:p-4 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0 w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-white rounded-lg overflow-hidden border border-gray-200">
                            <img src="{{ image_url($item->product->main_image) }}" 
                                 alt="{{ $item->product->name ?? 'Product' }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27100%27 height=%27100%27%3E%3Crect width=%27100%27 height=%27100%27 fill=%27%23f3f4f6%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 dominant-baseline=%27middle%27 text-anchor=%27middle%27 font-family=%27monospace%27 font-size=%2712px%27 fill=%27%239ca3af%27%3ENo Image%3C/text%3E%3C/svg%3E'">
                        </div>
                        <div class="flex-grow min-w-0">
                            <h3 class="text-xs sm:text-sm font-medium text-gray-900 line-clamp-2">{{ $item->product->name ?? 'Product' }}</h3>
                            @if($item->sku->attributes)
                                <p class="text-xs text-gray-500 mt-0.5 sm:mt-1">
                                    @php
                                        $attrs = is_array($item->sku->attributes) ? $item->sku->attributes : json_decode($item->sku->attributes, true);
                                        $displayAttrs = [];
                                        if (is_array($attrs)) {
                                            foreach ($attrs as $key => $value) {
                                                if ($key === 'image' || $value === null || ($key === 'image' && is_string($value) && str_contains($value, 'products/'))) {
                                                    continue;
                                                }
                                                if (is_string($value) && trim($value) !== '') {
                                                    $displayAttrs[$key] = $value;
                                                }
                                            }
                                        }
                                    @endphp
                                    @if(!empty($displayAttrs))
                                        @foreach($displayAttrs as $key => $value)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mr-1">
                                                Size: {{ $value }}
                                            </span>
                                        @endforeach
                                    @endif
                                </p>
                            @endif
                            <div class="flex items-center justify-between mt-1 sm:mt-2">
                                <span class="text-xs text-gray-600">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                <span class="text-xs sm:text-sm font-semibold text-green-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Summary -->
            <div class="p-3 sm:p-4 md:p-6 border-b border-gray-100">
                <h2 class="text-sm sm:text-base font-semibold text-gray-800 mb-2 sm:mb-4">Ringkasan Pesanan</h2>
                <div class="space-y-2">
                    <div class="flex justify-between text-xs sm:text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs sm:text-sm">
                        <span class="text-gray-600">Biaya Pengiriman</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs sm:text-sm">
                        <span class="text-gray-600">Biaya Layanan</span>
                        <span>Rp {{ number_format($order->service_fee, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="flex justify-between text-xs sm:text-sm">
                        <span class="text-gray-600">Diskon</span>
                        <span class="text-red-600">- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between pt-2 sm:pt-3 border-t border-gray-200 font-bold text-sm sm:text-base">
                        <span>Total</span>
                        <span class="text-green-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="p-3 sm:p-4 md:p-6 border-b border-gray-100">
                <h2 class="text-sm sm:text-base font-semibold text-gray-800 mb-2 sm:mb-4">Informasi Pengiriman</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3 md:gap-4">
                    <div>
                        <p class="text-xs text-gray-600">Nama Penerima</p>
                        <p class="text-xs sm:text-sm font-medium mt-0.5">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Nomor Telepon</p>
                        <p class="text-xs sm:text-sm font-medium mt-0.5">{{ $order->customer_phone }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-600">Alamat</p>
                        <p class="text-xs sm:text-sm font-medium mt-0.5 leading-relaxed">
                            {{ strtoupper($order->shipping_address) }},
                            {{ $order->shipping_district }}, 
                            {{ $order->shipping_subdistrict }},
                            {{ $order->shipping_city }}, 
                            {{ $order->shipping_province }} 
                            {{ $order->shipping_postal_code }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Metode Pengiriman</p>
                        <p class="text-xs sm:text-sm font-medium mt-0.5">{{ strtoupper($order->shipping_method) }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="p-3 sm:p-4 md:p-6">
                <h2 class="text-sm sm:text-base font-semibold text-gray-800 mb-2 sm:mb-4">Informasi Pembayaran</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3 md:gap-4">
                    <div>
                        <p class="text-xs text-gray-600">Metode Pembayaran</p>
                        <p class="text-xs sm:text-sm font-medium mt-0.5">{{ $order->getPaymentMethodLabel() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Status Pembayaran</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mt-0.5 {{ $order->getPaymentStatusBadgeClasses() }}">
                            {{ $order->getPaymentStatusShortLabel() }}
                        </span>
                    </div>
                    @if($order->paid_at)
                    <div>
                        <p class="text-xs text-gray-600">Tanggal Pembayaran</p>
                        <p class="text-xs sm:text-sm font-medium mt-0.5">{{ $order->paid_at->format('d M Y, H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="p-3 sm:p-4 md:p-6 flex flex-col sm:flex-row gap-2 sm:gap-3">
                <a href="{{ route('home') }}" 
                   class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-xs sm:text-sm rounded-lg hover:bg-gray-50 text-center transition font-medium">
                    Kembali ke Beranda
                </a>
                
                @if($order->getPaymentStatusEnum() !== \App\Enums\PaymentStatus::PAID)
                    @if($order->getPaymentStatusEnum() === \App\Enums\PaymentStatus::FAILED)
                    <a href="{{ route('catalog') }}" 
                       class="flex-1 px-4 py-2 bg-green-600 text-white text-xs sm:text-sm rounded-lg hover:bg-green-700 text-center transition font-medium">
                        Order Lagi
                    </a>
                    @else
                    <a href="{{ route('payment', $order->order_number) }}" 
                       class="flex-1 px-4 py-2 bg-green-600 text-white text-xs sm:text-sm rounded-lg hover:bg-green-700 text-center transition font-medium">
                        Lanjutkan Pembayaran
                    </a>
                    @endif
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