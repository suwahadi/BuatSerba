<section class="mb-12 sm:mb-16">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">
            @if($type === 'latest')
                Produk Terbaru
            @elseif($type === 'random')
                Produk Rekomendasi
            @else
                Produk Terlaris
            @endif
        </h2>
        <a href="/catalog?sortBy={{ $type === 'latest' ? 'newest' : ($type === 'random' ? 'random' : 'popularity') }}" class="text-sm sm:text-base text-green-600 hover:text-green-700 font-medium flex items-center">
            Lihat Semua
            <svg class="w-4 h-4 sm:w-5 sm:h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
        @forelse($products as $product)
        <div class="bg-white rounded-lg shadow-md card-hover overflow-hidden" wire:key="product-{{ $product->id }}">
            <a href="/product/{{ $product->slug }}" class="block">
                <div class="relative pb-[100%] bg-gray-100">
                    <img src="{{ product_image($product) }}" 
                         alt="{{ $product->name }}" 
                         class="absolute inset-0 w-full h-full object-cover"
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27400%27 height=%27400%27%3E%3Crect width=%27400%27 height=%27400%27 fill=%27%23f3f4f6%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 dominant-baseline=%27middle%27 text-anchor=%27middle%27 font-family=%27monospace%27 font-size=%2726px%27 fill=%27%239ca3af%27%3ENo Image%3C/text%3E%3C/svg%3E'">
                    @if($product->is_featured && $type !== 'latest')
                    <span class="absolute top-2 right-2 bg-green-600 text-white text-xs px-2 py-1 rounded-full">
                        Featured
                    </span>
                    @endif
                    @if($type === 'latest' && $product->created_at->diffInDays(now()) <= 7)
                    <span class="absolute top-2 right-2 bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                        New
                    </span>
                    @endif
                </div>
                <div class="p-3">
                    <p class="text-xs text-gray-500 mb-1">{{ $product->category->name ?? 'Uncategorized' }}</p>
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
                            <span class="text-xs text-gray-500 line-through">
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
                    <div class="mt-3 flex items-center text-yellow-400 text-xs">
                        @for($i = 0; $i < 5; $i++)
                        <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-span-full text-center py-12 text-gray-500">
            Belum ada produk tersedia
        </div>
        @endforelse
    </div>

    @if($products->count() < $total)
        <div class="flex justify-center mt-8">
            <button wire:click="loadMore" class="px-6 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-green-600 transition-colors shadow-sm">
                <span wire:loading.remove wire:target="loadMore">Lebih Banyak</span>
                <span wire:loading wire:target="loadMore">Memuat...</span>
            </button>
        </div>
    @endif
</section>
