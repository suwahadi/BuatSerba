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
                    </div>
                    <div class="flex-shrink-0">
                        <p class="text-base sm:text-xl md:text-2xl font-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                        <p class="mt-0.5 sm:mt-1 text-xs opacity-90 text-right">Total Pembayaran</p>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="p-3 sm:p-4 md:p-6 border-b border-gray-100">
                <h2 class="text-sm sm:text-base font-semibold text-gray-800 mb-2 sm:mb-4">Ringkasan Pesanan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3 md:gap-4">
                    <div>
                        <p class="text-xs text-gray-600">Nama Pemesan</p>
                        <p class="text-xs sm:text-sm font-medium mt-0.5">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Email</p>
                        <p class="text-xs sm:text-sm font-medium mt-0.5 break-all">{{ $order->customer_email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Metode Pembayaran</p>
                        <p class="text-xs sm:text-sm font-medium capitalize mt-0.5">{{ str_replace('-', ' ', $order->payment_method) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Status Pembayaran</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mt-0.5
                            @if($order->payment_status === 'paid') bg-green-100 text-green-800
                            @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment Instructions -->
            @if(isset($paymentInstructions) && !empty($paymentInstructions) && $order->payment_status !== 'paid' && $order->status !== 'cancelled')
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
                <a href="{{ route('home') }}" 
                   class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-xs sm:text-sm rounded-lg hover:bg-gray-50 text-center transition font-medium">
                    Kembali ke Beranda
                </a>
                
                @if($order->payment_status !== 'paid')
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