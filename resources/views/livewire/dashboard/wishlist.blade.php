<div>
    <h1 class="text-xl font-bold text-gray-900 mb-4">Wishlist Saya</h1>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 min-h-[600px]">
        @if($items->count() > 0)
            <p class="text-sm text-gray-500 mb-6">
                {{ $items->total() }} produk tersimpan di wishlist Anda.
            </p>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                @foreach($items as $entry)
                    @php
                        $sku = $entry->sku;
                        $product = $sku?->product;
                        $price = (float) ($sku?->selling_price ?? 0);
                        $base = (float) ($sku?->base_price ?? 0);
                        $hasDiscount = $sku && $base > $price;
                        $discountPct = $hasDiscount ? discount_percentage($base, $price) : 0;
                    @endphp
                    @if($product && $sku)
                        <article wire:key="wishlist-{{ $entry->id }}" class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow flex flex-col">
                            <a href="{{ route('product.detail', $product->slug) }}?sku={{ $sku->id }}" class="block">
                                <div class="relative aspect-square bg-gray-50">
                                    <img src="{{ product_image($product) }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover"
                                         loading="lazy"
                                         onerror="this.src='/images/placeholder.jpg'">
                                    @if($hasDiscount && $discountPct > 0)
                                        <span class="absolute top-2 right-2 bg-sale text-white text-[11px] font-extrabold px-1.5 py-0.5 rounded-md shadow-chip">-{{ $discountPct }}%</span>
                                    @endif
                                </div>
                            </a>
                            <div class="p-3 flex flex-col flex-grow">
                                <a href="{{ route('product.detail', $product->slug) }}?sku={{ $sku->id }}" class="block">
                                    <h3 class="text-sm font-medium text-gray-900 line-clamp-2 min-h-[2.5em] hover:text-emerald20-700 transition-colors">
                                        {{ $product->name }}
                                    </h3>
                                </a>
                                @if($sku->name)
                                    <p class="text-xs text-gray-500 mt-1">{{ $sku->name }}</p>
                                @endif
                                <div class="mt-2">
                                    <span class="font-mono font-bold text-emerald20-700 text-[15px] md:text-[16px] whitespace-nowrap">
                                        {{ format_rupiah($price) }}
                                    </span>
                                    @if($hasDiscount)
                                        <div class="text-[11px] md:text-[12px] line-through text-black/45 whitespace-nowrap">
                                            {{ format_rupiah($base) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-auto pt-3 flex gap-2">
                                    <a href="{{ route('product.detail', $product->slug) }}?sku={{ $sku->id }}"
                                       class="flex-1 text-center bg-emerald20-600 hover:bg-emerald20-700 text-white text-xs font-semibold py-2 rounded-md transition-colors">
                                        Lihat
                                    </a>
                                    <button wire:click="remove({{ $sku->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="remove({{ $sku->id }})"
                                            class="px-2.5 py-2 border border-gray-300 hover:border-red-500 hover:bg-red-50 hover:text-red-600 text-gray-500 rounded-md transition-colors"
                                            aria-label="Hapus dari Wishlist">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endif
                @endforeach
            </div>

            <div class="mt-6">
                {{ $items->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center text-center py-16">
                <div class="mb-4 text-gray-300">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 1 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1">
                    Belum Ada Produk di Wishlist
                </h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto text-xs leading-relaxed">
                    Telusuri katalog dan tekan ikon hati pada produk untuk menyimpannya di sini.
                </p>
                <a href="{{ route('catalog') }}"
                   class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-10 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600">
                    Mulai Belanja
                </a>
            </div>
        @endif
    </div>
</div>
