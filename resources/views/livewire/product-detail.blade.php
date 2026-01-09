<div>
<div class="bg-gray-50">
    <!-- Navigation -->
    <x-navbar />

    <!-- Breadcrumb -->
    <div class="pt-16 sm:pt-20 pb-3 sm:pb-4 bg-white border-b">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <nav class="flex overflow-x-auto" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm whitespace-nowrap">
                    <li><a href="/" class="text-gray-500 hover:text-gray-700">Beranda</a></li>
                    <li><span class="text-gray-400">/</span></li>
                    <li><a href="/catalog" class="text-gray-500 hover:text-gray-700">Katalog</a></li>
                    <li><span class="text-gray-400">/</span></li>
                    <li><a href="/{{ $product->category->slug }}" class="text-gray-500 hover:text-gray-700">{{ $product->category->name }}</a></li>
                    <li><span class="text-gray-400">/</span></li>
                    <li><span class="text-gray-900 font-medium truncate max-w-[150px] sm:max-w-none">{{ Str::limit($product->name, 30) }}</span></li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Product Detail Content -->
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-8">
            <!-- Product Images -->
            <div class="space-y-3 sm:space-y-4">
                <div class="bg-white rounded-lg shadow-lg p-3 sm:p-4">
                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                        <img src="{{ image_url($product->main_image) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27800%27 height=%27800%27%3E%3Crect width=%27800%27 height=%27800%27 fill=%27%23f3f4f6%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 dominant-baseline=%27middle%27 text-anchor=%27middle%27 font-family=%27monospace%27 font-size=%2748px%27 fill=%27%239ca3af%27%3ENo Image%3C/text%3E%3C/svg%3E'">
                    </div>
                </div>
                
                @if($product->images && count($product->images) > 1)
                <div class="grid grid-cols-4 gap-2">
                    @foreach(array_slice($product->images, 0, 4) as $image)
                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer hover:opacity-75 transition-opacity">
                        <img src="{{ image_url($image) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="space-y-4 sm:space-y-6">
                <div>
                    <div class="flex items-center space-x-2 mb-2">
                        @if($product->is_featured)
                        <span class="bg-red-500 text-white px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-xs sm:text-sm font-semibold">Best Seller</span>
                        @endif
                        <span class="text-xs sm:text-sm font-medium {{ $selectedSku && $selectedSku->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $selectedSku && $selectedSku->stock_quantity > 0 ? 'Tersedia' : 'Stok Habis' }}
                        </span>
                    </div>
                    
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-3 sm:mb-4">{{ $product->name }}</h1>
                    
                    <div class="flex items-center space-x-2 sm:space-x-4 mb-3 sm:mb-4">
                        <div class="flex items-center space-x-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($this->averageRating))
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @else
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-300 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-xs sm:text-sm text-gray-500">{{ number_format($product->view_count) }} dilihat</span>
                    </div>
                    
                    @if($selectedSku)
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-4 sm:mb-6">
                        <span class="text-2xl sm:text-3xl font-bold text-green-600">{{ format_rupiah($selectedSku->selling_price) }}</span>
                        @if($selectedSku->base_price > $selectedSku->selling_price)
                        <span class="text-base sm:text-xl text-gray-500 line-through">{{ format_rupiah($selectedSku->base_price) }}</span>
                        @php
                            $discountPercent = discount_percentage($selectedSku->base_price, $selectedSku->selling_price);
                        @endphp
                        @if($discountPercent > 0)
                        <span class="bg-red-100 text-red-800 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full text-xs sm:text-sm font-semibold">-{{ $discountPercent }}%</span>
                        @endif
                        @endif
                    </div>
                    
                    <!-- Dynamic Pricing Information -->
                    {{--
                    <div class="bg-blue-50 rounded-lg p-3 sm:p-4 mb-4 sm:mb-6">
                        <h3 class="text-sm sm:text-base font-semibold text-blue-900 mb-2 sm:mb-3">Harga Berdasarkan Jumlah Pembelian</h3>
                        <div class="space-y-2 sm:space-y-3">
                            @php
                                $pricingTiers = $selectedSku->getPricingTiersForDisplay();
                            @endphp
                            @foreach($pricingTiers as $tier)
                            <div class="flex justify-between items-center p-2 sm:p-3 bg-white rounded-lg border border-blue-100 cursor-pointer hover:bg-blue-100 transition-colors" 
                                 wire:click="addTierToCart({{ $tier['quantity'] }})">
                                <div>
                                    <span class="text-xs sm:text-sm font-medium text-gray-900">
                                        @if($tier['quantity'] == 1)
                                            Pembelian Eceran
                                        @else
                                            Pembelian {{ $tier['quantity'] }}+ pcs
                                        @endif
                                    </span>
                                    @if(isset($tier['label']))
                                    <span class="ml-1 sm:ml-2 inline-flex items-center px-1.5 sm:px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $tier['label'] }}
                                    </span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="text-sm sm:text-base font-bold text-green-600">{{ format_rupiah($tier['price']) }}</span>
                                    <span class="text-xs text-gray-500">/ pcs</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <p class="mt-2 sm:mt-3 text-xs sm:text-sm text-blue-700">
                            <svg class="inline w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            Klik pada tier untuk langsung menambahkan ke keranjang belanja.
                        </p>
                    </div>
                    --}}
                    @endif
                </div>

                <!-- Variant Selection -->
                @if(count($availableVariants) > 0)
                <div class="space-y-3 sm:space-y-4">
                    @foreach($availableVariants as $attributeName => $values)
                    <div>
                        <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-gray-900 mb-2 sm:mb-3">Pilih {{ ucfirst($attributeName) }}</h3>
                        <div class="flex flex-wrap gap-2 sm:gap-3">
                            @foreach($values as $value)
                            <button 
                                wire:click="selectVariant('{{ $attributeName }}', '{{ $value }}')"
                                class="px-3 sm:px-4 py-1.5 sm:py-2 text-sm border-2 rounded-lg font-medium transition-all {{ isset($selectedVariants[$attributeName]) && $selectedVariants[$attributeName] == $value ? 'border-green-600 bg-green-600 text-white' : 'border-gray-300 text-gray-700 hover:border-green-600' }}">
                                {{ $value }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Quantity Selection -->
                @if($selectedSku && $selectedSku->stock_quantity > 0)
                <div>
                    <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-gray-900 mb-2 sm:mb-3">Jumlah</h3>
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <div class="inline-flex items-center border border-gray-300 rounded-lg overflow-hidden hover:border-green-500 transition-colors bg-white shadow-sm">
                            <button wire:click="decrementQuantity" class="px-2.5 sm:px-3 py-1.5 sm:py-2 hover:bg-gray-100 active:bg-gray-200 transition-colors border-r border-gray-200">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <input type="number" wire:model.live="quantity" min="1" max="{{ $selectedSku->stock_quantity }}" 
                                   class="w-12 sm:w-16 text-center py-1.5 sm:py-2 text-sm sm:text-base font-semibold text-gray-900 focus:outline-none focus:bg-green-50 transition-colors border-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            <button wire:click="incrementQuantity" class="px-2.5 sm:px-3 py-1.5 sm:py-2 hover:bg-gray-100 active:bg-gray-200 transition-colors border-l border-gray-200">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                        <span class="text-xs sm:text-sm text-gray-600">Stok tersedia</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 pt-3 sm:pt-4">
                    <button wire:click="addToCart" class="flex-1 bg-green-600 text-white py-2.5 sm:py-3 rounded-lg text-sm sm:text-base font-semibold hover:bg-green-700 transition-colors flex items-center justify-center space-x-2">
                        <span>Tambah ke Keranjang</span>
                    </button>
                    <button wire:click="buyNow" class="flex-1 bg-orange-500 text-white py-2.5 sm:py-3 rounded-lg text-sm sm:text-base font-semibold hover:bg-orange-600 transition-colors">
                        Beli Sekarang
                    </button>
                    <button class="sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center sm:w-auto">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Branch Inventory Information -->
                {{--
                @if($branchInventory && $branchInventory->count() > 0)
                <div class="mt-4 sm:mt-6">
                    <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-gray-900 mb-2 sm:mb-3">Ketersediaan Stok di Gudang</h3>
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <ul class="divide-y divide-gray-200">
                            @foreach($branchInventory as $inventory)
                            <li class="px-3 sm:px-4 py-2.5 sm:py-3 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2 sm:ml-3">
                                        <p class="text-xs sm:text-sm font-medium text-gray-900">{{ $inventory->branch->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $inventory->branch->city_name }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    @if($inventory->quantity_available > 10)
                                        <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $inventory->quantity_available }} tersedia
                                        </span>
                                    @elseif($inventory->quantity_available > 0)
                                        <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $inventory->quantity_available }} tersedia
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Stok habis
                                        </span>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        <div class="bg-blue-50 px-3 sm:px-4 py-2.5 sm:py-3 text-xs sm:text-sm text-blue-700">
                            <svg class="inline w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            Pemilihan gudang akan dilakukan saat checkout.
                        </div>
                    </div>
                </div>
                @endif
                --}}
                @else
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 sm:p-4">
                    <p class="text-sm sm:text-base text-red-800 font-medium">Produk ini sedang tidak tersedia</p>
                </div>
                @endif

                <!-- Product Features -->
                @if($product->features)
                <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                    <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-2 sm:mb-3">Keunggulan Produk</h3>
                    <ul class="space-y-1.5 sm:space-y-2 text-xs sm:text-sm text-gray-700">
                        @foreach($product->features as $feature)
                        <li class="flex items-start">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

            </div>
        </div>

        <!-- Product Details Tabs -->
        <div class="mt-6 sm:mt-12 bg-white rounded-lg shadow-lg">
            <div class="border-b border-gray-200 overflow-x-auto">
                <nav class="flex space-x-4 sm:space-x-8 px-3 sm:px-6 min-w-max">
                    <button wire:click="setActiveTab('description')" 
                            class="py-3 sm:py-4 px-2 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap {{ $activeTab === 'description' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Deskripsi
                    </button>
                    <button wire:click="setActiveTab('reviews')" 
                            class="py-3 sm:py-4 px-2 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap {{ $activeTab === 'reviews' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Ulasan ({{ $this->reviewCount }})
                    </button>
                </nav>
            </div>

            <div class="p-4 sm:p-6">
                <!-- Description Tab -->
                <div class="{{ $activeTab === 'description' ? '' : 'hidden' }}">
                    <div class="product-description text-gray-700 text-sm sm:text-base leading-relaxed">
                        {!! $product->description !!}
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div class="{{ $activeTab === 'reviews' ? '' : 'hidden' }}">
                    <div class="space-y-6">
                        @forelse($product->reviews as $review)
                        <div class="border-b border-gray-100 pb-6 last:border-0">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                        <span class="text-xs font-bold text-gray-600">{{ substr($review->user->name ?? 'User', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-900">{{ $review->user->name ?? 'Pengguna' }}</h4>
                                        <span class="text-xs text-gray-500">{{ $review->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                                <div class="flex space-x-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }} fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $review->review }}</p>

                            @if(!empty($review->images) && is_array($review->images))
                            @php
                                $imageUrls = collect($review->images)->map(fn($img) => Storage::url($img))->toArray();
                            @endphp
                            <div class="mt-3 flex gap-2 overflow-x-auto pb-2" 
                                 x-data="{ 
                                    lightbox: false, 
                                    images: {{ json_encode($imageUrls) }},
                                    currentIndex: 0,
                                    get currentImage() { return this.images[this.currentIndex]; },
                                    next() { this.currentIndex = (this.currentIndex + 1) % this.images.length; },
                                    prev() { this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length; }
                                 }">
                                @foreach($review->images as $index => $image)
                                    <div class="relative w-16 h-16 sm:w-20 sm:h-20 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 cursor-pointer hover:opacity-90"
                                         @click="currentIndex = {{ $index }}; lightbox = true">
                                        <img src="{{ Storage::url($image) }}" class="w-full h-full object-cover">
                                    </div>
                                @endforeach

                                <!-- Simple Lightbox -->
                                <div x-show="lightbox" 
                                     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90 p-4"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     @click.self="lightbox = false"
                                     @keydown.window.escape="lightbox = false"
                                     @keydown.window.arrow-right="if(lightbox) next()"
                                     @keydown.window.arrow-left="if(lightbox) prev()"
                                     style="display: none;">
                                    
                                    <div class="relative max-w-4xl max-h-full flex items-center justify-center w-full">
                                        <!-- Prev Button -->
                                        <button x-show="images.length > 1" 
                                                @click.stop="prev()" 
                                                class="absolute left-0 sm:-left-16 text-white hover:text-gray-300 p-2 focus:outline-none z-10 bg-black/50 sm:bg-transparent rounded-full sm:rounded-none m-2 sm:m-0">
                                            <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                            </svg>
                                        </button>

                                        <img :src="currentImage" class="max-w-full max-h-[85vh] object-contain rounded-lg select-none">
                                        
                                        <!-- Next Button -->
                                        <button x-show="images.length > 1" 
                                                @click.stop="next()" 
                                                class="absolute right-0 sm:-right-16 text-white hover:text-gray-300 p-2 focus:outline-none z-10 bg-black/50 sm:bg-transparent rounded-full sm:rounded-none m-2 sm:m-0">
                                            <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>

                                        <!-- Close Button -->
                                        <button @click="lightbox = false" class="absolute -top-12 right-0 sm:-right-10 text-white hover:text-gray-300 p-2">
                                            <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                        
                                        <!-- Counter -->
                                        <div x-show="images.length > 1" class="absolute -bottom-10 left-0 right-0 text-center text-white text-sm select-none">
                                            <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <p class="text-gray-500">Belum ada ulasan untuk produk ini.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="mt-6 sm:mt-12">
            <div class="flex justify-between items-center mb-4 sm:mb-6">
                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">Produk Terkait</h3>
                <a href="/catalog?category={{ $product->category_id }}" class="text-sm sm:text-base text-green-600 font-semibold hover:text-green-700">Lihat Semua →</a>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
                @foreach($relatedProducts as $relatedProduct)
                <div class="bg-white rounded-lg shadow-md card-hover overflow-hidden">
                    <a href="/product/{{ $relatedProduct->slug }}" class="block">
                        <div class="relative pb-[100%] bg-gray-100">
                            <img src="{{ image_url($relatedProduct->main_image) }}" 
                                 alt="{{ $relatedProduct->name }}" 
                                 class="absolute inset-0 w-full h-full object-cover"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27400%27 height=%27400%27%3E%3Crect width=%27400%27 height=%27400%27 fill=%27%23f3f4f6%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 dominant-baseline=%27middle%27 text-anchor=%27middle%27 font-family=%27monospace%27 font-size=%2726px%27 fill=%27%239ca3af%27%3ENo Image%3C/text%3E%3C/svg%3E'">
                            @if($relatedProduct->is_featured)
                            <span class="absolute top-1.5 sm:top-2 right-1.5 sm:right-2 bg-green-600 text-white text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full">
                                Featured
                            </span>
                            @endif
                        </div>
                        <div class="p-2.5 sm:p-4">
                            <p class="text-xs text-gray-500 mb-0.5 sm:mb-1 truncate">{{ $relatedProduct->category->name ?? 'Uncategorized' }}</p>
                            <h3 class="text-xs sm:text-sm lg:text-base font-semibold text-gray-900 mb-1.5 sm:mb-2 line-clamp-2">{{ $relatedProduct->name }}</h3>
                            @php
                                $relatedSku = $relatedProduct->skus->first();
                            @endphp
                            @if($relatedSku)
                            <div class="mt-2 sm:mt-3">
                                <div class="flex flex-col sm:flex-row sm:items-baseline sm:space-x-2">
                                    <span class="text-sm sm:text-lg lg:text-xl font-bold text-green-600">
                                        {{ format_rupiah($relatedSku->selling_price) }}
                                    </span>
                                    @if($relatedSku->base_price > $relatedSku->selling_price)
                                    <span class="text-xs sm:text-sm text-gray-500 line-through">
                                        {{ format_rupiah($relatedSku->base_price) }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <x-footer />

    @if(session()->has('error'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 5000)"
         class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        {{ session('error') }}
    </div>
    @endif

    <style>
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .glass-nav { backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.9); }
        .quantity-btn { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: 1px solid #d1d5db; background: white; cursor: pointer; transition: all 0.2s; }
        .quantity-btn:hover { background: #f3f4f6; }
        .quantity-btn:active { transform: scale(0.95); }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
        
        /* Cart Notification Modal */
        .cart-notification-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            backdrop-filter: blur(4px);
        }
        
        .cart-notification-modal.active {
            opacity: 1;
            visibility: visible;
        }
        
        .cart-notification-content {
            background: white;
            border-radius: 20px;
            padding: 36px;
            max-width: 480px;
            width: 90%;
            text-align: center;
            transform: translateY(30px) scale(0.9);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 25px 50px -12px rgba(22, 163, 74, 0.25);
            border: 2px solid #16a34a;
        }
        
        .cart-notification-modal.active .cart-notification-content {
            transform: translateY(0) scale(1);
        }
        
        .cart-notification-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            border-radius: 50%;
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(22, 163, 74, 0.3);
            animation: successPulse 0.6s ease-out;
        }
        
        @keyframes successPulse {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .cart-notification-title {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }
        
        .cart-notification-subtitle {
            font-size: 15px;
            color: #6b7280;
            margin-bottom: 24px;
        }
        
        .cart-notification-product-info {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid #bbf7d0;
        }
        
        .cart-notification-buttons {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
        
        .cart-notification-btn {
            flex: 1;
            padding: 16px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 15px;
            border: none;
        }
        
        .view-cart-btn {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
        }
        
        .view-cart-btn:hover {
            background: linear-gradient(135deg, #15803d 0%, #166534 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(22, 163, 74, 0.4);
        }
        
        .view-cart-btn:active {
            transform: translateY(0);
        }
        
        .continue-shopping-btn {
            background: white;
            color: #4b5563;
            border: 2px solid #d1d5db;
        }
        
        .continue-shopping-btn:hover {
            background: #f9fafb;
            transform: translateY(-2px);
            border-color: #16a34a;
            color: #16a34a;
        }
        
        .continue-shopping-btn:active {
            transform: translateY(0);
        }

        /* Product Description Styles (Custom Typography) */
        .product-description p { margin-bottom: 1em; line-height: 1.6; font-size: 14px;}
        .product-description p:last-child { margin-bottom: 0; }
        .product-description ul { list-style-type: disc; padding-left: 1.5em; margin-bottom: 1em; }
        .product-description ol { list-style-type: decimal; padding-left: 1.5em; margin-bottom: 1em; }
        .product-description li { margin-bottom: 0.25em; padding-left: 0.25em; }
        .product-description strong, .product-description b { font-weight: 600; color: #111827; }
        .product-description em, .product-description i { font-style: italic; }
        .product-description h1 { font-size: 1.5em; font-weight: 700; margin-top: 1.5em; margin-bottom: 0.75em; color: #111827; }
        .product-description h2 { font-size: 1.25em; font-weight: 700; margin-top: 1.25em; margin-bottom: 0.75em; color: #111827; }
        .product-description h3 { font-size: 1.125em; font-weight: 600; margin-top: 1.25em; margin-bottom: 0.5em; color: #111827; }
        .product-description blockquote { border-left: 4px solid #e5e7eb; padding-left: 1em; margin-left: 0; margin-right: 0; font-style: italic; color: #4b5563; }
        .product-description img { max-width: 100%; height: auto; border-radius: 0.5rem; margin: 1em 0; }
        .product-description a { color: #16a34a; text-decoration: underline; }
        .product-description a:hover { color: #15803d; }
    </style>
    
    <!-- Cart Notification Modal -->
    <div x-data="{ 
        open: false, 
        productName: '',
        quantity: 0,
        price: 0,
        show(productName, quantity, price) {
            this.open = true;
            this.productName = productName;
            this.quantity = quantity;
            this.price = price;
        },
        hide() {
            this.open = false;
        },
        goToCart() {
            window.location.href = '/cart';
        }
    }" 
    x-on:show-cart-notification.window="show($event.detail.productName, $event.detail.quantity, $event.detail.price)"
    class="cart-notification-modal" 
    :class="{ 'active': open }"
    @click.self="hide()">
        <div class="cart-notification-content">
            <!-- Success Icon -->
            <div class="cart-notification-icon">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <!-- Title & Subtitle -->
            <h3 class="cart-notification-title">Item Berhasil Ditambahkan!</h3>
            <p class="cart-notification-subtitle">Produk telah dimasukkan ke keranjang belanja Anda</p>
            
            <!-- Action Buttons -->
            <div class="cart-notification-buttons">
                <button @click="goToCart()" class="cart-notification-btn view-cart-btn">
                    Lihat Keranjang
                </button>
                <button @click="hide()" class="cart-notification-btn continue-shopping-btn">
                    Lanjut Belanja
                </button>
            </div>
        </div>
    </div>
</div>
</div>
