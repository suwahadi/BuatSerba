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
                        <input type="text" wire:model.live.debounce.300ms="search" 
                               placeholder="Cari produk, brand, atau kategori..." 
                               class="w-full px-4 py-2 pl-10 pr-4 text-gray-700 bg-white border border-gray-300 rounded-full focus:outline-none focus:border-green-500">
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Navigation Links -->
                <div class="flex items-center space-x-6">
                    <a href="/" class="text-gray-700 hover:text-green-600 font-medium">Beranda</a>
                    <a href="/cart" class="text-gray-700 hover:text-green-600 font-medium relative">
                        Keranjang
                        <span class="cart-count absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center" style="display: none;">0</span>
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
                    <li><span class="text-gray-900 font-medium">Katalog</span></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filters Sidebar -->
            <div class="lg:w-1/4">
                <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-semibold text-gray-900">Filter</h3>
                            @php
                                $activeFilterCount = count($selectedCategories) + count($selectedBrands) + count($selectedRatings);
                                if ($minPrice > 0 || $maxPrice < 50000000) $activeFilterCount++;
                                if ($search) $activeFilterCount++;
                            @endphp
                            @if($activeFilterCount > 0)
                            <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">
                                {{ $activeFilterCount }}
                            </span>
                            @endif
                        </div>
                        @if($activeFilterCount > 0)
                        <button wire:click="clearFilters" 
                                wire:loading.attr="disabled"
                                class="text-sm text-green-600 hover:text-green-700 font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="clearFilters">Hapus Semua</span>
                            <span wire:loading wire:target="clearFilters">Menghapus...</span>
                        </button>
                        @endif
                    </div>

                    <!-- Active Filters Tags -->
                    @if($activeFilterCount > 0)
                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <div class="flex flex-wrap gap-2">
                            @if($search)
                            <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 text-xs px-2 py-1 rounded-full">
                                Pencarian: "{{ Str::limit($search, 15) }}"
                                <button wire:click="$set('search', '')" class="hover:text-green-900">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </span>
                            @endif
                            @foreach($selectedCategories as $catId)
                                @php $cat = $categories->firstWhere('id', $catId); @endphp
                                @if($cat)
                                <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full">
                                    {{ $cat->name }}
                                    <button wire:click="toggleCategory({{ $catId }})" class="hover:text-blue-900">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </span>
                                @endif
                            @endforeach
                            @foreach($selectedBrands as $brand)
                            <span class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 text-xs px-2 py-1 rounded-full">
                                {{ ucfirst($brand) }}
                                <button wire:click="toggleBrand('{{ $brand }}')" class="hover:text-purple-900">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </span>
                            @endforeach
                            @if($minPrice > 0 || $maxPrice < 50000000)
                            <span class="inline-flex items-center gap-1 bg-orange-50 text-orange-700 text-xs px-2 py-1 rounded-full">
                                Rp {{ number_format($minPrice, 0, ',', '.') }} - Rp {{ number_format($maxPrice, 0, ',', '.') }}
                                <button wire:click="$set('minPrice', 0); $set('maxPrice', 50000000)" class="hover:text-orange-900">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Category Filter -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">Kategori</h4>
                        <div class="space-y-2">
                            @foreach($categories as $category)
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       wire:model.live="selectedCategories" 
                                       value="{{ $category->id }}" 
                                       class="rounded text-green-600 focus:ring-green-500">
                                <span class="ml-2 text-gray-700">{{ $category->name }}</span>
                                <span class="ml-auto text-sm text-gray-500">({{ $category->products_count ?? 0 }})</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">Harga</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Rp {{ number_format($minPrice, 0, ',', '.') }}</span>
                                <span>Rp {{ number_format($maxPrice, 0, ',', '.') }}</span>
                            </div>
                            <input type="range" 
                                   wire:model.live.debounce.500ms="maxPrice" 
                                   class="range-slider w-full" 
                                   min="0" 
                                   max="50000000" 
                                   step="100000">
                            <div class="grid grid-cols-2 gap-2 mt-3">
                                <input type="number" 
                                       wire:model.live.debounce.500ms="minPrice" 
                                       placeholder="Min" 
                                       class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                                <input type="number" 
                                       wire:model.live.debounce.500ms="maxPrice" 
                                       placeholder="Max" 
                                       class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Brand Filter -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">Brand</h4>
                        <div class="space-y-2">
                            @foreach(['Samsung', 'Apple', 'Sony', 'Logitech', 'Anker', 'ASUS', 'Nike'] as $brand)
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       wire:model.live="selectedBrands" 
                                       value="{{ strtolower($brand) }}" 
                                       class="rounded text-green-600 focus:ring-green-500">
                                <span class="ml-2 text-gray-700">{{ $brand }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Rating Filter -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">Rating</h4>
                        <div class="space-y-2">
                            @for($i = 5; $i >= 4; $i--)
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       wire:model.live="selectedRatings" 
                                       value="{{ $i }}" 
                                       class="rounded text-green-600 focus:ring-green-500">
                                <span class="ml-2 flex text-yellow-400">
                                    @for($j = 0; $j < $i; $j++)
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                    @for($j = $i; $j < 5; $j++)
                                    <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </span>
                                <span class="ml-2 text-gray-700">{{ $i }} Bintang {{ $i < 5 ? '& Up' : '' }}</span>
                            </label>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:w-3/4">
                <!-- Sort and View Options -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-700">Urutkan:</span>
                            <select wire:model.live="sortBy" 
                                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                                <option value="popularity">Populer</option>
                                <option value="newest">Terbaru</option>
                                <option value="price-low">Harga: Rendah ke Tinggi</option>
                                <option value="price-high">Harga: Tinggi ke Rendah</option>
                                <option value="rating">Rating Tertinggi</option>
                            </select>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Tampilan:</span>
                            <button wire:click="setViewMode('grid')" 
                                    class="p-2 rounded-md {{ $viewMode === 'grid' ? 'bg-green-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                            </button>
                            <button wire:click="setViewMode('list')" 
                                    class="p-2 rounded-md {{ $viewMode === 'list' ? 'bg-green-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search Results Info -->
                <div class="mb-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Hasil Pencarian</h2>
                            <p class="text-gray-600 text-sm mt-1">Menampilkan <span>{{ $products->total() }}</span> produk</p>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="grid {{ $viewMode === 'grid' ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3' : 'grid-cols-1' }} gap-6">
                    @forelse($products as $product)
                    <div class="bg-white rounded-lg shadow-md card-hover overflow-hidden">
                        <a href="/product/{{ $product->slug }}" class="block">
                            <div class="relative pb-[100%] bg-gray-100">
                                <img src="{{ image_url($product->main_image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="absolute inset-0 w-full h-full object-cover"
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27400%27 height=%27400%27%3E%3Crect width=%27400%27 height=%27400%27 fill=%27%23f3f4f6%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 dominant-baseline=%27middle%27 text-anchor=%27middle%27 font-family=%27monospace%27 font-size=%2726px%27 fill=%27%239ca3af%27%3ENo Image%3C/text%3E%3C/svg%3E'">
                                @if($product->is_featured)
                                <span class="absolute top-2 right-2 bg-green-600 text-white text-xs px-2 py-1 rounded-full">
                                    Featured
                                </span>
                                @endif
                            </div>
                            <div class="p-4">
                                <p class="text-xs text-gray-500 mb-1">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                <h3 class="text-base font-semibold text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                                @php
                                    $sku = $product->skus->first();
                                @endphp
                                @if($sku)
                                <div class="mt-3">
                                    <div class="flex items-baseline space-x-2">
                                        <span class="text-xl font-bold text-green-600">
                                            {{ format_rupiah($sku->selling_price) }}
                                        </span>
                                        @if($sku->base_price > $sku->selling_price)
                                        <span class="text-sm text-gray-500 line-through">
                                            {{ format_rupiah($sku->base_price) }}
                                        </span>
                                        @php
                                            $discountPercent = discount_percentage($sku->base_price, $sku->selling_price);
                                        @endphp
                                        @if($discountPercent > 0)
                                        <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">
                                            -{{ $discountPercent }}%
                                        </span>
                                        @endif
                                        @endif
                                    </div>
                                    
                                    <!-- Wholesale Info -->
                                    @if($sku->wholesale_price && $sku->wholesale_min_quantity)
                                    <div class="mt-2 text-xs text-blue-600 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                        </svg>
                                        Grosir: {{ format_rupiah($sku->wholesale_price) }} untuk {{ $sku->wholesale_min_quantity }}+ pcs
                                    </div>
                                    @endif
                                </div>
                                @endif
                                <div class="mt-3 flex items-center justify-between">
                                    <div class="flex items-center text-yellow-400 text-sm">
                                        @for($i = 0; $i < 5; $i++)
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                        <span class="ml-1 text-gray-600">({{ rand(10, 500) }})</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada produk ditemukan</h3>
                        <p class="mt-1 text-sm text-gray-500">Coba ubah filter atau kata kunci pencarian Anda</p>
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->links() }}
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
                        @foreach($categories->take(4) as $category)
                        <li><a href="/catalog?category={{ $category->id }}" class="hover:text-white">{{ $category->name }}</a></li>
                        @endforeach
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

    <style>
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .glass-nav { backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.9); }
        .range-slider { -webkit-appearance: none; appearance: none; height: 6px; background: #e5e7eb; border-radius: 3px; outline: none; }
        .range-slider::-webkit-slider-thumb { -webkit-appearance: none; appearance: none; width: 20px; height: 20px; background: #00AA5B; border-radius: 50%; cursor: pointer; }
        .range-slider::-moz-range-thumb { width: 20px; height: 20px; background: #00AA5B; border-radius: 50%; cursor: pointer; border: none; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</div>
