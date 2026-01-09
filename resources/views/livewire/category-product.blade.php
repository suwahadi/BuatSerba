<div class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <x-navbar />

    <!-- Header / Breadcrumb can go here -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
            @if($category->description)
                <p class="mt-2 text-gray-600">{{ $category->description }}</p>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($products->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6">
                @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden group">
                    <a href="{{ route('product.detail', $product->slug) }}" class="block">
                        <div class="relative pb-[100%] bg-gray-100">
                            <img src="{{ image_url($product->main_image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27400%27 height=%27400%27%3E%3Crect width=%27400%27 height=%27400%27 fill=%27%23f3f4f6%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 dominant-baseline=%27middle%27 text-anchor=%27middle%27 font-family=%27monospace%27 font-size=%2726px%27 fill=%27%239ca3af%27%3ENo Image%3C/text%3E%3C/svg%3E'">
                            @if($product->is_featured)
                            <span class="absolute top-2 right-2 bg-green-600 text-white text-xs px-2 py-1 rounded-full z-10">
                                Featured
                            </span>
                            @endif
                        </div>
                        <div class="p-3 sm:p-4">
                            <p class="text-xs text-gray-500 mb-1 truncate">{{ $category->name }}</p>
                            <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 h-10">{{ $product->name }}</h3>
                            
                            @php
                                $sku = $product->skus->first();
                            @endphp
                            
                            @if($sku)
                            <div class="mt-2">
                                <div class="flex flex-col sm:flex-row sm:items-baseline sm:space-x-1">
                                    <span class="text-sm sm:text-base font-bold text-green-600">
                                        {{ format_rupiah($sku->selling_price) }}
                                    </span>
                                </div>
                                
                                @if($sku->base_price > $sku->selling_price)
                                <div class="flex items-center space-x-1 mt-1">
                                    <span class="text-xs text-gray-400 line-through">
                                        {{ format_rupiah($sku->base_price) }}
                                    </span>
                                    @php
                                        $discountPercent = discount_percentage($sku->base_price, $sku->selling_price);
                                    @endphp
                                    @if($discountPercent > 0)
                                    <span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full font-bold">
                                        -{{ $discountPercent }}%
                                    </span>
                                    @endif
                                </div>
                                @endif
                            </div>
                            @endif
                            
                            <div class="mt-3 flex items-center space-x-0.5">
                                @php
                                    $rating = $product->reviews_avg_rating ?? 0;
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($rating))
                                        <svg class="w-3 h-3 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @else
                                        <svg class="w-3 h-3 text-gray-300 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endif
                                @endfor
                                <span class="text-xs text-gray-400 ml-1">({{ $product->reviews_count }})</span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-16 bg-white rounded-lg shadow-sm border border-gray-100">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada produk</h3>
                <p class="text-gray-500">Belum ada produk untuk kategori ini.</p>
                <a href="/catalog" class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                    Lihat Semua Produk
                </a>
            </div>
        @endif
    </div>

    <x-footer />
</div>
