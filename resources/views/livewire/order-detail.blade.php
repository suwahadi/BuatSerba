<div x-data="{ showToast: false, toastMessage: '' }">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">Detail Pesanan</h1>
                        <div class="flex items-center mt-2 space-x-2">
                            <p class="opacity-90">Order ID: #{{ $order->order_number }}</p>
                            <button @click="
                                navigator.clipboard.writeText('{{ $order->order_number }}');
                                showToast = true;
                                toastMessage = 'Order ID berhasil disalin!';
                                setTimeout(() => showToast = false, 3000);
                            " class="p-1 hover:bg-green-500 rounded transition-colors" title="Salin Order ID">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                        <p class="mt-1 text-sm opacity-90">Total Pembayaran</p>
                    </div>
                </div>
            </div>

            <!-- Order Status -->
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Status Pesanan</h2>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            @if($order->status === 'delivered') bg-green-100 text-green-800
                            @elseif($order->status === 'shipped') bg-blue-100 text-blue-800
                            @elseif($order->status === 'processing') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'pending') bg-purple-100 text-purple-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Tanggal Pemesanan</p>
                        <p class="font-medium">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Item Pesanan</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0 w-20 h-20 bg-white rounded-lg overflow-hidden border border-gray-200">
                            <img src="{{ image_url($item->product->main_image) }}" 
                                 alt="{{ $item->product->name ?? 'Product' }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27100%27 height=%27100%27%3E%3Crect width=%27100%27 height=%27100%27 fill=%27%23f3f4f6%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 dominant-baseline=%27middle%27 text-anchor=%27middle%27 font-family=%27monospace%27 font-size=%2712px%27 fill=%27%239ca3af%27%3ENo Image%3C/text%3E%3C/svg%3E'">
                        </div>
                        <div class="flex-grow">
                            <h3 class="font-medium text-gray-900">{{ $item->product->name ?? 'Product' }}</h3>
                            @if($item->sku->attributes)
                                <p class="text-sm text-gray-500 mt-1">
                                    @if(is_string($item->sku->attributes))
                                        {{ implode(', ', json_decode($item->sku->attributes, true)) }}
                                    @elseif(is_array($item->sku->attributes))
                                        @foreach($item->sku->attributes as $key => $value)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mr-1">
                                                {{ $key }}: {{ $value }}
                                            </span>
                                        @endforeach
                                    @else
                                        {{ $item->sku->attributes }}
                                    @endif
                                </p>
                            @endif
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-sm text-gray-600">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                <span class="font-semibold text-green-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Summary -->
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Pesanan</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Biaya Pengiriman</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Biaya Layanan</span>
                        <span>Rp {{ number_format($order->service_fee, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Diskon</span>
                        <span class="text-red-600">- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between pt-3 border-t border-gray-200 font-bold text-lg">
                        <span>Total</span>
                        <span class="text-green-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pengiriman</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Nama Penerima</p>
                        <p class="font-medium">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Nomor Telepon</p>
                        <p class="font-medium">{{ $order->customer_phone }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600">Alamat</p>
                        <p class="font-medium">
                            {{ $order->shipping_address }}, 
                            {{ $order->shipping_district }}, 
                            {{ $order->shipping_city }}, 
                            {{ $order->shipping_province }} 
                            {{ $order->shipping_postal_code }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Metode Pengiriman</p>
                        <p class="font-medium">{{ ucfirst(str_replace('-', ' ', $order->shipping_method)) }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pembayaran</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Metode Pembayaran</p>
                        <p class="font-medium">{{ ucfirst(str_replace('-', ' ', $order->payment_method)) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status Pembayaran</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($order->payment_status === 'paid') bg-green-100 text-green-800
                            @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                    @if($order->paid_at)
                    <div>
                        <p class="text-sm text-gray-600">Tanggal Pembayaran</p>
                        <p class="font-medium">{{ $order->paid_at->format('d M Y, H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="p-6 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('home') }}" 
                   class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-center transition font-medium">
                    Kembali ke Beranda
                </a>
                
                @if($order->payment_status !== 'paid')
                <a href="{{ route('payment', $order->order_number) }}" 
                   class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-center transition font-medium">
                    Lanjutkan Pembayaran
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
         class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2"
         style="display: none;">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span x-text="toastMessage"></span>
    </div>
</div>