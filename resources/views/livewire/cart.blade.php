<div class="bg-gray-50">
    <!-- Navigation -->
    <x-navbar :cartCount="$cartItems->count()" />

    <!-- Breadcrumb -->
    <div class="pt-16 sm:pt-20 pb-3 sm:pb-4 bg-white border-b">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm">
                    <li><a href="/" class="text-gray-500 hover:text-gray-700">Beranda</a></li>
                    <li><span class="text-gray-400">/</span></li>
                    <li><span class="text-gray-900 font-medium">Keranjang</span></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8">
        <!-- Cart Steps -->
        <div class="mb-4 sm:mb-8 overflow-x-auto">
            <div class="flex items-center justify-center space-x-2 sm:space-x-4 md:space-x-8 min-w-max px-4">
                <div class="flex items-center space-x-1 sm:space-x-2">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold bg-green-600 text-white">1</div>
                    <span class="text-xs sm:text-sm font-medium hidden sm:inline">Keranjang</span>
                </div>
                <div class="w-8 sm:w-16 h-0.5 sm:h-1 bg-gray-200"></div>
                <div class="flex items-center space-x-1 sm:space-x-2">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold bg-gray-200 text-gray-600">2</div>
                    <span class="text-xs sm:text-sm font-medium text-gray-600 hidden sm:inline">Checkout</span>
                </div>
                <div class="w-8 sm:w-16 h-0.5 sm:h-1 bg-gray-200"></div>
                <div class="flex items-center space-x-1 sm:space-x-2">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold bg-gray-200 text-gray-600">3</div>
                    <span class="text-xs sm:text-sm font-medium text-gray-600 hidden sm:inline">Pembayaran</span>
                </div>
                <div class="w-8 sm:w-16 h-0.5 sm:h-1 bg-gray-200"></div>
                <div class="flex items-center space-x-1 sm:space-x-2">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold bg-gray-200 text-gray-600">4</div>
                    <span class="text-xs sm:text-sm font-medium text-gray-600 hidden sm:inline">Selesai</span>
                </div>
            </div>
        </div>

        <!-- Cart Content -->
        <div class="grid grid-cols-1 {{ $cartItems->count() > 0 ? 'lg:grid-cols-3' : '' }} gap-8">
            <!-- Main Content -->
            <div class="{{ $cartItems->count() > 0 ? 'lg:col-span-2' : 'w-full' }}">
                <div class="bg-white rounded-lg shadow-lg p-3 sm:p-6">
                    <h2 class="text-lg sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">Keranjang Belanja</h2>
                    
                    @if($cartItems->isEmpty())
                    <!-- Empty Cart State -->
                    <div class="text-center py-8 sm:py-12">
                        <h3 class="mt-4 text-base sm:text-lg font-medium text-gray-900">Keranjang belanja Anda kosong</h3>
                        <p class="mt-2 text-xs sm:text-sm text-gray-500">Mulai berbelanja dan tambahkan produk ke keranjang Anda</p>
                        <div class="mt-6">
                            <a href="/catalog" class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 border border-transparent text-sm sm:text-base font-medium rounded-lg text-white bg-green-600 hover:bg-green-700">
                                Mulai Belanja
                            </a>
                        </div>
                    </div>
                    @else
                    <!-- Cart Items -->
                    <div class="space-y-3 sm:space-y-4">
                        @foreach($cartItems as $item)
                        <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4 p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-green-500 transition-colors" wire:key="cart-item-{{ $item->id }}">
                            <div class="flex items-start space-x-3 sm:space-x-4 flex-1 min-w-0">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    <img src="{{ image_url($item->product->main_image) }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg"
                                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27100%27 height=%27100%27%3E%3Crect width=%27100%27 height=%27100%27 fill=%27%23f3f4f6%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 dominant-baseline=%27middle%27 text-anchor=%27middle%27 font-family=%27monospace%27 font-size=%2712px%27 fill=%27%239ca3af%27%3ENo Image%3C/text%3E%3C/svg%3E'">
                                </div>

                                <!-- Product Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm sm:text-base font-semibold text-gray-900 line-clamp-2 sm:truncate">
                                        <a href="/product/{{ $item->product->slug }}" class="hover:text-green-600">
                                            {{ $item->product->name }}
                                        </a>
                                    </h3>
                                    @if($item->sku->attributes)
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1">
                                        @foreach($item->sku->attributes as $key => $value)
                                            <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mr-1">
                                                {{ $key }}: {{ $value }}
                                            </span>
                                        @endforeach
                                    </p>
                                    @endif
                                    <p class="text-base sm:text-lg font-bold text-green-600 mt-1 sm:mt-2">{{ format_rupiah($item->price) }}</p>
                                    @if($item->sku->stock_quantity < 5)
                                    <p class="text-xs text-red-600 mt-1">Stok tersisa: {{ $item->sku->stock_quantity }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Quantity Controls & Actions -->
                            <div class="flex items-center justify-between sm:justify-end space-x-3 sm:space-x-4 w-full sm:w-auto">
                                <div class="inline-flex items-center border border-gray-300 rounded-lg overflow-hidden hover:border-green-500 transition-colors bg-white shadow-sm">
                                    <button wire:click="decrementQuantity({{ $item->id }})" 
                                            wire:loading.attr="disabled"
                                            class="px-2 sm:px-3 py-1.5 sm:py-2 hover:bg-gray-100 active:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed border-r border-gray-200">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <input type="number" 
                                           wire:change="updateQuantity({{ $item->id }}, $event.target.value)"
                                           value="{{ $item->quantity }}"
                                           min="1" 
                                           max="{{ $item->sku->stock_quantity }}" 
                                           class="w-12 sm:w-16 text-center py-1.5 sm:py-2 text-sm sm:text-base font-semibold text-gray-900 focus:outline-none focus:bg-green-50 transition-colors border-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                    <button wire:click="incrementQuantity({{ $item->id }})" 
                                            wire:loading.attr="disabled"
                                            class="px-2 sm:px-3 py-1.5 sm:py-2 hover:bg-gray-100 active:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed border-l border-gray-200">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Subtotal & Remove -->
                                <div class="flex flex-col items-end space-y-1 sm:space-y-2">
                                    <p class="text-base sm:text-lg font-bold text-gray-900">{{ format_rupiah($item->price * $item->quantity) }}</p>
                                    <button wire:click="removeItem({{ $item->id }})" 
                                            wire:confirm="Apakah Anda yakin ingin menghapus item ini?"
                                            class="text-xs sm:text-sm text-red-600 hover:text-red-700 flex items-center">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
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
            @if($cartItems->count() > 0)
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 sticky top-20 sm:top-24">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Ringkasan Pesanan</h3>
                    
                    <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6">
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600">Subtotal ({{ $cartItems->count() }} item)</span>
                            <span class="font-medium">{{ format_rupiah($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span class="text-gray-500 italic text-xs">Dihitung saat checkout</span>
                        </div>
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600">Biaya Layanan</span>
                            <span class="font-medium">{{ format_rupiah($serviceFee) }}</span>
                        </div>
                        @if($discount > 0)
                        <div class="flex justify-between text-xs sm:text-sm text-green-600">
                            <span>Diskon</span>
                            <span class="font-medium">-{{ format_rupiah($discount) }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <div class="border-t border-gray-200 pt-3 sm:pt-4 mb-4 sm:mb-6">
                        <div class="flex justify-between text-base sm:text-lg font-semibold">
                            <span>Total</span>
                            <span class="text-green-600">{{ format_rupiah($total) }}</span>
                        </div>
                    </div>
                    
                    <!-- Promo Code -->
                    <div class="mb-4 sm:mb-6">
                        <div class="flex space-x-2">
                            <input type="text" 
                                   wire:model="promoCode"
                                   placeholder="Kode promo" 
                                   class="flex-1 px-2.5 sm:px-3 py-1.5 sm:py-2 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:border-green-500">
                            <button wire:click="applyPromoCode" 
                                    wire:loading.attr="disabled"
                                    class="bg-gray-200 text-gray-700 px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm hover:bg-gray-300 transition-colors disabled:opacity-50 whitespace-nowrap">
                                <span wire:loading.remove wire:target="applyPromoCode">Terapkan</span>
                                <span wire:loading wire:target="applyPromoCode">...</span>
                            </button>
                        </div>
                        <!-- <p class="text-xs text-gray-500 mt-2">DISKON10, DISKON50K, WELCOME</p> -->
                    </div>
                    
                    <!-- Action Buttons -->
                    @if($cartItems->count() > 0)
                    <div class="space-y-2 sm:space-y-3">
                        <button wire:click="checkout" 
                                class="w-full bg-green-600 text-white py-2.5 sm:py-3 rounded-lg text-sm sm:text-base font-semibold hover:bg-green-700 transition-colors">
                            Lanjut ke Pembayaran
                        </button>
                        <a href="/catalog" 
                           class="block w-full text-center border border-gray-300 text-gray-700 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                            Lanjut Belanja
                        </a>
                    </div>
                    @endif
                    
                    <!-- Security Badges -->
                    <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-center space-x-2 text-xs text-gray-500">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Transaksi aman dengan SSL</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <x-footer />

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
        /* Custom styles can be added here if needed */
    </style>
</div>
