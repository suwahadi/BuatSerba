{{--
    Product list — restyled to design's `pcard` look while preserving Livewire component contract:
    - $type: 'latest' | 'random' | 'best-selling'
    - $products: collection of App\Models\Product (with category + skus eager-loaded)
    - $total: full count for "load more" gating
    - loadMore() Livewire action
--}}
<div>
    @php
        $eyebrow = match($type) {
            'latest'       => ['caption' => 'Restock Hari Ini', 'caption_color' => 'text-violet10-500', 'title' => 'Produk Terbaru'],
            'random'       => ['caption' => 'Disesuaikan untukmu', 'caption_color' => 'text-violet10-500', 'title' => 'Pilihan untuk Kamu'],
            default        => ['caption' => 'Best Seller', 'caption_color' => 'text-tan5-600', 'title' => 'Produk Terlaris'],
        };
        $catalogSort = $type === 'latest' ? 'newest' : ($type === 'random' ? 'random' : 'popularity');
        $isBest = $type === 'best-selling';
    @endphp
    <section class="container-x mt-12 md:mt-20 product-list-section" aria-label="{{ $eyebrow['title'] }}">
        <div class="flex items-end justify-between mb-4 md:mb-6">
            <div class="flex items-center gap-3">
                <div>
                    <p class="text-[12px] uppercase tracking-[0.2em] {{ $eyebrow['caption_color'] }} font-semibold">{{ $eyebrow['caption'] }}</p>
                    <h2 class="font-display font-extrabold text-[24px] md:text-[34px] tracking-tight">{{ $eyebrow['title'] }}</h2>
                </div>
            </div>
            <a href="{{ route('catalog') }}?sortBy={{ $catalogSort }}" class="inline-flex items-center gap-1.5 text-emerald20-700 font-semibold text-sm hover:underline shrink-0">
                <span class="hidden sm:inline">Lihat Semua</span>
                <span class="sm:hidden">Semua</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14"/><path d="m13 5 7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
            @forelse($products as $idx => $product)
                @php
                    $sku = $product->skus->first();
                    $flashItem = ($flashMap ?? collect())->get($product->id);
                    $isFlash = $flashItem && ! $flashItem->is_sold_out;
                    if ($isFlash) {
                        $price = (float) $flashItem->flash_price;
                        $base = (float) ($flashItem->original_price_snapshot ?: ($sku?->selling_price ?? 0));
                        $hasDiscount = $base > $price;
                        $discountPct = $flashItem->discount_percentage;
                    } else {
                        $price = $sku?->selling_price ?? 0;
                        $base = $sku?->base_price ?? 0;
                        $hasDiscount = $sku && $base > $price;
                        $discountPct = $hasDiscount ? discount_percentage($base, $price) : 0;
                    }
                    $phN = ($idx % 6) + 1;
                    // Tier chip from price
                    if ($price <= 25000) {
                        $tierCls = 'tier-25'; $tierLabel = 'Serba 25';
                    } elseif ($price <= 35000) {
                        $tierCls = 'tier-35'; $tierLabel = 'Serba 35';
                    } elseif ($price <= 45000) {
                        $tierCls = 'tier-45'; $tierLabel = 'Serba 45';
                    } else {
                        $tierCls = 'tier-mix'; $tierLabel = 'Serba-Serbi';
                    }
                    $isNew = $type === 'latest' && $product->created_at && $product->created_at->diffInDays(now()) <= 7;
                @endphp
                <article wire:key="product-{{ $product->id }}" class="pcard relative bg-white rounded-2xl shadow-card overflow-hidden border border-black/[0.04] focus-within:ring-2 focus-within:ring-emerald20-300">
                    @if($sku)
                        <livewire:components.wishlist-button
                            :sku-id="$sku->id"
                            variant="card"
                            :is-active-initial="in_array($sku->id, $wishlistedSkuIds ?? [])"
                            :key="'wb-list-'.$type.'-'.$product->id" />
                    @endif
                    <a href="{{ route('product.detail', $product->slug) }}" class="block focus:outline-none">
                        <div class="relative ph-{{ $phN }} aspect-square overflow-hidden">
                            <img loading="lazy" class="pimg w-full h-full object-cover" src="{{ product_image($product) }}" alt="{{ $product->name }}" onerror="this.src='/images/placeholder.jpg'">

                            @if($hasDiscount && $discountPct > 0)
                                <span class="absolute top-11 right-2 bg-sale text-white text-[11px] font-extrabold px-1.5 py-0.5 rounded-md shadow-chip">-{{ $discountPct }}%</span>
                            @endif

                            @if($isFlash)
                                <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-gradient-to-r from-sale to-tan5-500 shadow-chip">⚡Flash Sale</span>
                            @elseif($isBest)
                                <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-gradient-to-r from-violet10-500 to-tan5-400 shadow-chip">Best Seller</span>
                            @elseif($isNew)
                                <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-emerald20-600 shadow-chip">Baru</span>
                            @elseif($product->is_featured ?? false)
                                <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-violet10-500 shadow-chip">Pilihan</span>
                            @endif
                        </div>
                        <div class="p-3 md:p-4">
                            @if($price > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $tierCls }}">{{ $tierLabel }}</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tier-mix">{{ $product->category->name ?? 'Kategori' }}</span>
                            @endif
                            <h3 class="mt-1.5 text-[13px] md:text-[14px] font-medium leading-snug text-ink line-clamp-2 min-h-[2.6em]">{{ $product->name }}</h3>
                            @if($sku)
                                <div class="mt-1.5">
                                    <span class="font-mono font-extrabold text-emerald20-700 text-[15px] md:text-[16px] whitespace-nowrap">{{ format_rupiah($price) }}</span>
                                </div>
                                @if($hasDiscount)
                                    <div class="text-[11px] md:text-[12px] strike text-black/45 whitespace-nowrap">{{ format_rupiah($base) }}</div>
                                @endif
                            @endif
                            <div class="mt-2 flex items-center gap-2 text-[11px] text-black/55">
                                <span class="inline-flex items-center gap-0.5 text-emerald20-600 font-semibold">
                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    {{ $product->avg_rating ?? '4.8' }}
                                </span>
                                <span class="opacity-30">·</span>
                                <span>Terjual {{ $product->view_count > 0 ? number_format($product->view_count) : (rand(50, 950)) }}</span>
                            </div>
                        </div>
                    </a>
                </article>
            @empty
                <div class="col-span-full text-center py-12 text-black/55">
                    Belum ada produk tersedia
                </div>
            @endforelse
        </div>

        @if($products->count() < $total)
            <div class="flex justify-center mt-6 md:mt-8">
                <button wire:click="loadMore" class="px-6 py-2.5 bg-white border border-black/10 rounded-xl text-sm font-semibold text-ink hover:bg-paper hover:text-emerald20-700 transition-colors shadow-card">
                    <span wire:loading.remove wire:target="loadMore">Lebih Banyak</span>
                    <span wire:loading wire:target="loadMore">Memuat…</span>
                </button>
            </div>
        @endif
    </section>
</div>
