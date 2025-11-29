<div class="bg-gray-50">
    <!-- Navigation -->
    <nav class="glass-nav fixed top-0 w-full z-50 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-green-600">BuatSerba</a>
                </div>
                
                <!-- Search Bar -->
                <div class="flex-1 max-w-2xl mx-8">
                    <div class="relative">
                        <input type="text" placeholder="Cari produk, brand, atau kategori..." 
                               class="w-full px-4 py-2 pl-10 pr-4 text-gray-700 bg-white border border-gray-300 rounded-full focus:outline-none focus:border-green-500">
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Navigation Links -->
                <div class="flex items-center space-x-6">
                    <a href="/" class="text-gray-700 hover:text-green-600 font-medium">Beranda</a>
                    <a href="/catalog" class="text-gray-700 hover:text-green-600 font-medium">Katalog</a>
                    <a href="/cart" class="text-green-600 font-medium relative">
                        Keranjang
                        @if($cartItems->count() > 0)
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                            {{ $cartItems->count() }}
                        </span>
                        @endif
                    </a>
                    <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        Masuk
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <div class="pt-20 pb-4 bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="/" class="text-gray-500 hover:text-gray-700">Beranda</a></li>
                    <li><span class="text-gray-400">/</span></li>
                    <li><span class="text-gray-900 font-medium">Keranjang Belanja</span></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Cart Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-8">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold bg-green-600 text-white">1</div>
                    <span class="text-sm font-medium">Keranjang</span>
                </div>
                <div class="w-16 h-1 bg-gray-200"></div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold bg-gray-200 text-gray-600">2</div>
                    <span class="text-sm font-medium text-gray-600">Pengiriman</span>
                </div>
                <div class="w-16 h-1 bg-gray-200"></div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold bg-gray-200 text-gray-600">3</div>
                    <span class="text-sm font-medium text-gray-600">Pembayaran</span>
                </div>
                <div class="w-16 h-1 bg-gray-200"></div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold bg-gray-200 text-gray-600">4</div>
                    <span class="text-sm font-medium text-gray-600">Selesai</span>
                </div>
            </div>
        </div>

        <!-- Cart Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Keranjang Belanja</h2>
                    
                    @if($cartItems->isEmpty())
                    <!-- Empty Cart State -->
                    <div class="text-center py-12">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13v6a2 2 0 002 2h6a2 2 0 002-2v-6"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Keranjang belanja Anda kosong</h3>
                        <p class="mt-2 text-sm text-gray-500">Mulai berbelanja dan tambahkan produk ke keranjang Anda.</p>
                        <div class="mt-6">
                            <a href="/catalog" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-green-600 hover:bg-green-700">
                                Mulai Belanja
                            </a>
                        </div>
                    </div>
                    @else
                    <!-- Cart Items -->
                    <div class="space-y-4">
                        @foreach($cartItems as $item)
                        <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-green-500 transition-colors" wire:key="cart-item-{{ $item->id }}">
                            <!-- Product Image -->
                            <div class="flex-shrink-0">
                                <img src="{{ image_url($item->product->main_image) }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="w-20 h-20 object-cover rounded-lg"
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27100%27 height=%27100%27%3E%3Crect width=%27100%27 height=%27100%27 fill=%27%23f3f4f6%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 dominant-baseline=%27middle%27 text-anchor=%27middle%27 font-family=%27monospace%27 font-size=%2712px%27 fill=%27%239ca3af%27%3ENo Image%3C/text%3E%3C/svg%3E'">
                            </div>

                            <!-- Product Info -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-gray-900 truncate">
                                    <a href="/product/{{ $item->product->slug }}" class="hover:text-green-600">
                                        {{ $item->product->name }}
                                    </a>
                                </h3>
                                @if($item->sku->attributes)
                                <p class="text-sm text-gray-500 mt-1">
                                    @foreach($item->sku->attributes as $key => $value)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mr-1">
                                            {{ $key }}: {{ $value }}
                                        </span>
                                    @endforeach
                                </p>
                                @endif
                                <p class="text-lg font-bold text-green-600 mt-2">{{ format_rupiah($item->price) }}</p>
                                @if($item->sku->stock_quantity < 5)
                                <p class="text-xs text-red-600 mt-1">Stok tersisa: {{ $item->sku->stock_quantity }}</p>
                                @endif
                            </div>

                            <!-- Quantity Controls -->
                            <div class="flex items-center space-x-3">
                                <button wire:click="decrementQuantity({{ $item->id }})" 
                                        wire:loading.attr="disabled"
                                        class="quantity-btn rounded-l-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <input type="number" 
                                       wire:model.live.debounce.500ms="cartItems.{{ $loop->index }}.quantity"
                                       wire:change="updateQuantity({{ $item->id }}, $event.target.value)"
                                       value="{{ $item->quantity }}"
                                       min="1" 
                                       max="{{ $item->sku->stock_quantity }}" 
                                       class="w-16 text-center border-t border-b border-gray-300 py-2 focus:outline-none focus:border-green-500">
                                <button wire:click="incrementQuantity({{ $item->id }})" 
                                        wire:loading.attr="disabled"
                                        class="quantity-btn rounded-r-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Subtotal & Remove -->
                            <div class="flex flex-col items-end space-y-2">
                                <p class="text-lg font-bold text-gray-900">{{ format_rupiah($item->price * $item->quantity) }}</p>
                                <button wire:click="removeItem({{ $item->id }})" 
                                        wire:confirm="Apakah Anda yakin ingin menghapus item ini?"
                                        class="text-sm text-red-600 hover:text-red-700 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Cart Actions -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <a href="/catalog" class="text-sm text-green-600 hover:text-green-700 font-medium">
                                ← Lanjut Belanja
                            </a>
                            <button wire:click="clearCart" 
                                    wire:confirm="Apakah Anda yakin ingin mengosongkan keranjang?"
                                    class="text-sm text-red-600 hover:text-red-700 font-medium">
                                Hapus Semua
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar - Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h3>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal ({{ $cartItems->count() }} item)</span>
                            <span>{{ format_rupiah($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span>{{ $shippingCost > 0 ? format_rupiah($shippingCost) : 'GRATIS' }}</span>
                        </div>
                        @if($shippingCost == 0 && $subtotal > 0)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-2">
                            <p class="text-xs text-green-700">🎉 Selamat! Anda mendapat gratis ongkir</p>
                        </div>
                        @elseif($subtotal > 0 && $subtotal < 500000)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-2">
                            <p class="text-xs text-blue-700">Belanja {{ format_rupiah(500000 - $subtotal) }} lagi untuk gratis ongkir!</p>
                        </div>
                        @endif
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Biaya Layanan</span>
                            <span>{{ format_rupiah($serviceFee) }}</span>
                        </div>
                        @if($discount > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Diskon</span>
                            <span>-{{ format_rupiah($discount) }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4 mb-6">
                        <div class="flex justify-between text-lg font-semibold">
                            <span>Total</span>
                            <span class="text-green-600">{{ format_rupiah($total) }}</span>
                        </div>
                    </div>
                    
                    <!-- Promo Code -->
                    <div class="mb-6">
                        <div class="flex space-x-2">
                            <input type="text" 
                                   wire:model="promoCode"
                                   placeholder="Kode promo" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-green-500">
                            <button wire:click="applyPromoCode" 
                                    wire:loading.attr="disabled"
                                    class="bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm hover:bg-gray-300 transition-colors disabled:opacity-50">
                                <span wire:loading.remove wire:target="applyPromoCode">Terapkan</span>
                                <span wire:loading wire:target="applyPromoCode">...</span>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Coba: DISKON10, DISKON50K, WELCOME</p>
                    </div>
                    
                    <!-- Action Buttons -->
                    @if($cartItems->count() > 0)
                    <div class="space-y-3">
                        <button wire:click="checkout" 
                                class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                            Lanjut ke Pembayaran
                        </button>
                        <a href="/catalog" 
                           class="block w-full text-center border border-gray-300 text-gray-700 py-2 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                            Lanjut Belanja
                        </a>
                    </div>
                    @endif
                    
                    <!-- Security Badges -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-center space-x-2 text-xs text-gray-500">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Transaksi aman dengan SSL</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-2xl font-bold text-green-600 mb-4">BuatSerba</h4>
                    <p class="text-gray-400 mb-4">Platform belanja online terpercaya dengan produk berkualitas dan harga terbaik.</p>
                </div>
                
                <div>
                    <h5 class="font-semibold mb-4">Kategori</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Smartphone</a></li>
                        <li><a href="#" class="hover:text-white">Laptop</a></li>
                        <li><a href="#" class="hover:text-white">Audio</a></li>
                        <li><a href="#" class="hover:text-white">Gaming</a></li>
                    </ul>
                </div>
                
                <div>
                    <h5 class="font-semibold mb-4">Layanan</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Bantuan</a></li>
                        <li><a href="#" class="hover:text-white">Kebijakan Pengembalian</a></li>
                        <li><a href="#" class="hover:text-white">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-white">Kebijakan Privasi</a></li>
                    </ul>
                </div>
                
                <div>
                    <h5 class="font-semibold mb-4">Hubungi Kami</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li>Email: support@buatserba.com</li>
                        <li>Telepon: 0800-123-4567</li>
                        <li>Jam Operasional: 24/7</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} BuatSerba. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Flash Messages -->
    @if(session()->has('message'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 3000)"
         class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    @if(session()->has('error'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 3000)"
         class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
        </svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <style>
        .glass-nav { backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.9); }
        .quantity-btn { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: 1px solid #d1d5db; background: white; cursor: pointer; transition: all 0.2s; }
        .quantity-btn:hover { background: #f3f4f6; }
        .quantity-btn:active { transform: scale(0.95); }
        .quantity-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    </style>
</div>
