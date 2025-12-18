<div>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">Detail Pesanan</h1>
                        <p class="mt-1 opacity-90">Order #{{ $order->order_number }}</p>
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
                        <div class="flex-shrink-0 w-16 h-16 bg-white rounded-md flex items-center justify-center">
                            @if($item->product && $item->product->images && $item->product->images->first())
                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="w-12 h-12 object-contain">
                            @else
                                <div class="bg-gray-200 border-2 border-dashed rounded-xl w-12 h-12"></div>
                            @endif
                        </div>
                        <div class="flex-grow">
                            <h3 class="font-medium text-gray-900">{{ $item->product->name ?? 'Product' }}</h3>
                            @if($item->sku->attributes)
                                <p class="text-sm text-gray-500">
                                    @if(is_string($item->sku->attributes))
                                        {{ implode(', ', json_decode($item->sku->attributes, true)) }}
                                    @elseif(is_array($item->sku->attributes))
                                        {{ implode(', ', $item->sku->attributes) }}
                                    @else
                                        {{ $item->sku->attributes }}
                                    @endif
                                </p>
                            @endif
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-sm text-gray-600">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                <span class="font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
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
                    <div class="flex justify-between pt-3 border-t border-gray-200 font-bold">
                        <span>Total</span>
                        <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
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
                   class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-center transition">
                    Kembali ke Beranda
                </a>
                
                @if($order->payment_status !== 'paid')
                <a href="{{ route('payment', $order->order_number) }}" 
                   class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center transition">
                    Lanjutkan Pembayaran
                </a>
                @endif
            </div>
        </div>
    </div>
</div>