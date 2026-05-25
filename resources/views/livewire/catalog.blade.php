<div class="bg-white"
     x-data="{ filterOpen: @entangle('showMobileFilters') }"
     x-effect="document.body.style.overflow = filterOpen ? 'hidden' : ''">

    <x-navbar />

    @php
        $activeFilterCount = count($selectedCategories) + count($selectedBrands);
        if ($minPrice > 0 || $maxPrice < 50000000) $activeFilterCount++;
        if ($search) $activeFilterCount++;
        if ($flashOnly) $activeFilterCount++;
    @endphp

    {{-- Breadcrumb --}}
    <nav class="border-b border-black/5" aria-label="Breadcrumb">
        <div class="container-x py-2.5">
            <ol class="flex items-center gap-1.5 text-[12px]">
                <li><a href="/" class="text-black/55 hover:text-emerald20-600 transition-colors">Beranda</a></li>
                <li class="text-black/30">/</li>
                <li class="font-semibold text-ink">Katalog</li>
            </ol>
        </div>
    </nav>

    {{-- Page header --}}
    <header class="container-x pt-5 sm:pt-7 pb-4">
        <p class="text-[11px] uppercase tracking-[0.2em] text-tan5-600 font-semibold">Belanja</p>
        <div class="flex flex-wrap items-end justify-between gap-3 mt-1">
            <div>
                <h1 class="font-display font-extrabold text-[22px] md:text-[28px] tracking-tight text-ink">Katalog Produk</h1>
                <p class="text-[12px] sm:text-[13px] text-black/55 mt-0.5">
                    Menampilkan <span class="font-semibold text-ink">{{ number_format($products->total()) }}</span> produk
                </p>
            </div>
        </div>
    </header>

    {{-- Main area --}}
    <div class="container-x pb-10 md:pb-14">
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">

            {{-- ===== Desktop Sidebar ===== --}}
            <aside class="hidden lg:block w-[260px] xl:w-[280px] shrink-0">
                <div class="bg-white rounded-2xl shadow-card border border-black/[0.04] p-5 sticky top-24">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-2">
                            <h3 class="font-display font-bold text-[16px] text-ink">Filter</h3>
                            @if($activeFilterCount > 0)
                                <span class="bg-emerald20-100 text-emerald20-700 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $activeFilterCount }}</span>
                            @endif
                        </div>
                        @if($activeFilterCount > 0)
                            <button wire:click="clearFilters" wire:loading.attr="disabled"
                                    class="text-[12px] text-emerald20-600 hover:text-emerald20-700 font-semibold transition-colors disabled:opacity-50">
                                <span wire:loading.remove wire:target="clearFilters">Reset</span>
                                <span wire:loading wire:target="clearFilters">Menghapus...</span>
                            </button>
                        @endif
                    </div>

                    {{-- Active filter chips --}}
                    @if($activeFilterCount > 0)
                        <div class="mb-5 pb-5 border-b border-black/5">
                            <div class="flex flex-wrap gap-1.5">
                                @if($search)
                                    <span class="inline-flex items-center gap-1 bg-paper text-ink text-[11px] px-2 py-1 rounded-full border border-black/5">
                                        "{{ Str::limit($search, 15) }}"
                                        <button wire:click="$set('search', '')" class="text-black/50 hover:text-ink">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        </button>
                                    </span>
                                @endif
                                @if($flashOnly)
                                    <span class="inline-flex items-center gap-1 bg-sale/10 text-sale text-[11px] px-2 py-1 rounded-full border border-sale/20 font-semibold">
                                        Flash Sale
                                        <button wire:click="clearFlashFilter" class="hover:opacity-70">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        </button>
                                    </span>
                                @endif
                                @foreach($selectedCategories as $catId)
                                    @php $cat = $categories->firstWhere('id', $catId); @endphp
                                    @if($cat)
                                        <span class="inline-flex items-center gap-1 bg-emerald20-50 text-emerald20-700 text-[11px] px-2 py-1 rounded-full border border-emerald20-100">
                                            {{ $cat->name }}
                                            <button wire:click="toggleCategory({{ $catId }})" class="hover:text-emerald20-900">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                            </button>
                                        </span>
                                    @endif
                                @endforeach
                                @if($minPrice > 0 || $maxPrice < 50000000)
                                    <span class="inline-flex items-center gap-1 bg-tan5-50 text-tan5-700 text-[11px] px-2 py-1 rounded-full border border-tan5-100">
                                        {{ format_rupiah($minPrice) }}–{{ format_rupiah($maxPrice) }}
                                        <button wire:click="$set('minPrice', 0); $set('maxPrice', 50000000)" class="hover:text-tan5-900">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        </button>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Kategori --}}
                    <div class="mb-6">
                        <h4 class="font-display font-semibold text-[12px] text-ink mb-2.5 uppercase tracking-[0.12em]">Kategori</h4>
                        <div class="space-y-1 max-h-56 overflow-y-auto no-scrollbar -mx-1 px-1">
                            @foreach($categories as $category)
                                <label class="flex items-center gap-2.5 py-1 cursor-pointer group">
                                    <input type="checkbox" wire:model.live="selectedCategories" value="{{ $category->id }}"
                                           class="w-4 h-4 rounded border-black/20 text-emerald20-600 focus:ring-emerald20-500 focus:ring-2 focus:ring-offset-0 cursor-pointer">
                                    <span class="text-[13px] text-ink/85 group-hover:text-ink transition-colors flex-1">{{ $category->name }}</span>
                                    <span class="text-[11px] text-black/40 font-mono">{{ $category->products_count ?? 0 }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Harga --}}
                    <div>
                        <h4 class="font-display font-semibold text-[12px] text-ink mb-2.5 uppercase tracking-[0.12em]">Harga</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-[11px] font-mono text-black/55">
                                <span>{{ format_rupiah($minPrice) }}</span>
                                <span>{{ format_rupiah($maxPrice) }}</span>
                            </div>
                            <input type="range" wire:model.live.debounce.500ms="maxPrice" class="range-slider w-full"
                                   min="0" max="50000000" step="100000">
                            <div class="grid grid-cols-2 gap-2 pt-1">
                                <label class="block">
                                    <span class="text-[10px] text-black/45 uppercase tracking-wider font-semibold">Min</span>
                                    <input type="number" wire:model.live.debounce.500ms="minPrice" placeholder="0"
                                           class="w-full mt-0.5 px-2.5 py-1.5 bg-paper border border-black/10 rounded-lg text-[12px] font-mono text-ink focus:outline-none focus:border-emerald20-500 focus:bg-white">
                                </label>
                                <label class="block">
                                    <span class="text-[10px] text-black/45 uppercase tracking-wider font-semibold">Max</span>
                                    <input type="number" wire:model.live.debounce.500ms="maxPrice" placeholder="50.000.000"
                                           class="w-full mt-0.5 px-2.5 py-1.5 bg-paper border border-black/10 rounded-lg text-[12px] font-mono text-ink focus:outline-none focus:border-emerald20-500 focus:bg-white">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- ===== Main Content ===== --}}
            <div class="flex-1 min-w-0">

                {{-- Filter & Sort toolbar --}}
                <div class="flex items-center gap-2 mb-4">
                    {{-- Mobile filter trigger --}}
                    <button wire:click="toggleMobileFilters"
                            class="lg:hidden inline-flex items-center justify-center gap-2 px-3.5 py-2 bg-white border border-black/10 rounded-xl text-[13px] font-semibold text-ink hover:border-emerald20-500 hover:text-emerald20-600 transition-colors shadow-card shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M3 6h18M6 12h12M10 18h4" stroke-linecap="round"/></svg>
                        <span>Filter</span>
                        @if($activeFilterCount > 0)
                            <span class="bg-emerald20-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">{{ $activeFilterCount }}</span>
                        @endif
                    </button>

                    {{-- Sort --}}
                    <div class="flex items-center gap-2 ml-auto">
                        <label for="sort-by" class="text-[12px] text-black/55 shrink-0 hidden sm:inline">Urutkan</label>
                        <select id="sort-by" wire:model.live="sortBy"
                                class="bg-white border border-black/10 rounded-xl pl-3 pr-8 py-2 text-[13px] font-medium text-ink focus:outline-none focus:border-emerald20-500 shadow-card cursor-pointer appearance-none bg-no-repeat"
                                style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%2354402A%22 stroke-width=%222.5%22><path d=%22m6 9 6 6 6-6%22/></svg>'); background-position: right 10px center;">
                            <option value="popularity">Populer</option>
                            <option value="newest">Terbaru</option>
                            <option value="price-low">Harga: Termurah</option>
                            <option value="price-high">Harga: Termahal</option>
                        </select>

                        {{-- View toggle (desktop only) --}}
                        <div class="hidden md:flex items-center gap-0.5 bg-white border border-black/10 rounded-xl p-1 shadow-card">
                            <button wire:click="setViewMode('grid')"
                                    class="p-1.5 rounded-lg transition-all {{ $viewMode === 'grid' ? 'bg-emerald20-600 text-white' : 'text-black/55 hover:text-ink' }}"
                                    aria-label="Tampilan grid">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2h-2z"/></svg>
                            </button>
                            <button wire:click="setViewMode('list')"
                                    class="p-1.5 rounded-lg transition-all {{ $viewMode === 'list' ? 'bg-emerald20-600 text-white' : 'text-black/55 hover:text-ink' }}"
                                    aria-label="Tampilan list">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Mobile active filter chips --}}
                @if($activeFilterCount > 0)
                    <div class="lg:hidden flex flex-wrap items-center gap-1.5 mb-4">
                        @if($search)
                            <span class="inline-flex items-center gap-1 bg-paper text-ink text-[11px] px-2 py-1 rounded-full border border-black/5">
                                "{{ Str::limit($search, 12) }}"
                                <button wire:click="$set('search', '')" class="text-black/50">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                </button>
                            </span>
                        @endif
                        @if($flashOnly)
                            <span class="inline-flex items-center gap-1 bg-sale/10 text-sale text-[11px] px-2 py-1 rounded-full border border-sale/20 font-semibold">
                                Flash Sale
                                <button wire:click="clearFlashFilter">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                </button>
                            </span>
                        @endif
                        @foreach($selectedCategories as $catId)
                            @php $cat = $categories->firstWhere('id', $catId); @endphp
                            @if($cat)
                                <span class="inline-flex items-center gap-1 bg-emerald20-50 text-emerald20-700 text-[11px] px-2 py-1 rounded-full border border-emerald20-100">
                                    {{ $cat->name }}
                                    <button wire:click="toggleCategory({{ $catId }})">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    </button>
                                </span>
                            @endif
                        @endforeach
                        @if($minPrice > 0 || $maxPrice < 50000000)
                            <span class="inline-flex items-center gap-1 bg-tan5-50 text-tan5-700 text-[11px] px-2 py-1 rounded-full border border-tan5-100">
                                {{ format_rupiah($minPrice) }}–{{ format_rupiah($maxPrice) }}
                                <button wire:click="$set('minPrice', 0); $set('maxPrice', 50000000)">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                </button>
                            </span>
                        @endif
                        <button wire:click="clearFilters" class="text-[11px] text-emerald20-600 font-semibold underline-offset-2 hover:underline ml-1">Reset</button>
                    </div>
                @endif

                {{-- Products Grid --}}
                <div class="grid {{ $viewMode === 'grid' ? 'grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4' : 'grid-cols-1' }} gap-3 md:gap-4">
                    @forelse($products as $product)
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
                            $phN = ($loop->index % 6) + 1;
                            if ($price <= 25000) { $tierCls = 'tier-25'; $tierLabel = 'Serba 25'; }
                            elseif ($price <= 35000) { $tierCls = 'tier-35'; $tierLabel = 'Serba 35'; }
                            elseif ($price <= 45000) { $tierCls = 'tier-45'; $tierLabel = 'Serba 45'; }
                            else { $tierCls = 'tier-mix'; $tierLabel = 'Serba-Serbi'; }
                            $isNew = $product->created_at && $product->created_at->diffInDays(now()) <= 7;
                            $rating = $product->reviews_avg_rating ?? 0;
                        @endphp
                        <article wire:key="product-{{ $product->id }}" class="pcard relative bg-white rounded-2xl shadow-card overflow-hidden border border-black/[0.04] focus-within:ring-2 focus-within:ring-emerald20-300">
                            @if($sku)
                                <livewire:components.wishlist-button
                                    :sku-id="$sku->id"
                                    variant="card"
                                    :is-active-initial="in_array($sku->id, $wishlistedSkuIds ?? [])"
                                    :key="'wb-cat-'.$product->id" />
                            @endif
                            <a href="{{ route('product.detail', $product->slug) }}" class="block focus:outline-none">
                                <div class="relative ph-{{ $phN }} aspect-square overflow-hidden">
                                    <img loading="lazy" class="pimg w-full h-full object-cover" src="{{ product_image($product) }}" alt="{{ $product->name }}" onerror="this.src='/images/placeholder.jpg'">
                                    @if($hasDiscount && $discountPct > 0)
                                        <span class="absolute top-11 right-2 bg-sale text-white text-[11px] font-extrabold px-1.5 py-0.5 rounded-md shadow-chip">-{{ $discountPct }}%</span>
                                    @endif
                                    @if($isFlash)
                                        <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-gradient-to-r from-sale to-tan5-500 shadow-chip">⚡ Flash Sale</span>
                                    @elseif($product->is_featured)
                                        <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-violet10-500 shadow-chip">Pilihan</span>
                                    @elseif($isNew)
                                        <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-emerald20-600 shadow-chip">Baru</span>
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
                                            {{ $rating > 0 ? number_format($rating, 1) : '4.8' }}
                                        </span>
                                        <span class="opacity-30">·</span>
                                        <span>Terjual {{ $product->view_count > 0 ? number_format($product->view_count) : 0 }}</span>
                                    </div>
                                </div>
                            </a>
                        </article>
                    @empty
                        <div class="col-span-full bg-paper/60 border border-black/5 rounded-2xl text-center py-16 px-6">
                            <svg class="mx-auto h-12 w-12 text-black/30" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="mt-3 font-display font-bold text-[16px] text-ink">Produk tidak ditemukan</h3>
                            <p class="mt-1 text-[13px] text-black/55 max-w-sm mx-auto">Coba ubah filter atau kata kunci pencarian Anda.</p>
                            @if($activeFilterCount > 0)
                                <button wire:click="clearFilters" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-emerald20-600 hover:bg-emerald20-700 text-white text-[13px] font-semibold rounded-xl transition-colors">
                                    Reset filter
                                </button>
                            @endif
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($products->hasPages())
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== Mobile Filter Drawer ===== --}}
    <div class="lg:hidden">
        {{-- Backdrop --}}
        <div x-show="filterOpen" x-cloak
             x-on:click="filterOpen = false"
             x-transition.opacity.duration.200ms
             class="fixed inset-0 bg-black/45 z-40"></div>

        {{-- Drawer --}}
        <div x-show="filterOpen" x-cloak
             x-transition:enter="transition transform duration-300 ease-out"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition transform duration-200 ease-in"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="fixed inset-x-0 bottom-0 z-50 bg-white rounded-t-2xl shadow-2xl flex flex-col"
             style="max-height: 85vh; overscroll-behavior: contain;">

            {{-- Drag handle --}}
            <div class="pt-2.5 pb-1 flex justify-center shrink-0">
                <span class="block w-10 h-1.5 rounded-full bg-black/15"></span>
            </div>

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 pb-3 shrink-0">
                <h3 class="font-display font-bold text-[17px] text-ink">Filter & Urutkan</h3>
                <button x-on:click="filterOpen = false" class="-mr-2 p-2 text-black/55 hover:text-ink" aria-label="Tutup filter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Scrollable content --}}
            <div class="overflow-y-auto flex-1 px-5 pb-3" style="overscroll-behavior: contain;">
                {{-- Sort --}}
                <section class="mb-5">
                    <h4 class="font-display font-semibold text-[12px] text-ink mb-2 uppercase tracking-[0.12em]">Urutkan</h4>
                    <select wire:model.live="sortBy"
                            class="w-full bg-paper border border-black/10 rounded-xl px-3 py-2.5 text-[13px] font-medium text-ink focus:outline-none focus:border-emerald20-500">
                        <option value="popularity">Populer</option>
                        <option value="newest">Terbaru</option>
                        <option value="price-low">Harga: Termurah</option>
                        <option value="price-high">Harga: Termahal</option>
                    </select>
                </section>

                {{-- Active filter chips --}}
                @if($activeFilterCount > 0)
                    <section class="mb-5 pb-5 border-b border-black/5">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-display font-semibold text-[12px] text-ink uppercase tracking-[0.12em]">Aktif ({{ $activeFilterCount }})</h4>
                            <button wire:click="clearFilters" class="text-[12px] text-emerald20-600 font-semibold">Hapus Semua</button>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @if($search)
                                <span class="inline-flex items-center gap-1 bg-paper text-ink text-[11px] px-2 py-1 rounded-full border border-black/5">
                                    "{{ Str::limit($search, 15) }}"
                                    <button wire:click="$set('search', '')"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
                                </span>
                            @endif
                            @foreach($selectedCategories as $catId)
                                @php $cat = $categories->firstWhere('id', $catId); @endphp
                                @if($cat)
                                    <span class="inline-flex items-center gap-1 bg-emerald20-50 text-emerald20-700 text-[11px] px-2 py-1 rounded-full border border-emerald20-100">
                                        {{ $cat->name }}
                                        <button wire:click="toggleCategory({{ $catId }})"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
                                    </span>
                                @endif
                            @endforeach
                            @if($minPrice > 0 || $maxPrice < 50000000)
                                <span class="inline-flex items-center gap-1 bg-tan5-50 text-tan5-700 text-[11px] px-2 py-1 rounded-full border border-tan5-100">
                                    {{ format_rupiah($minPrice) }}–{{ format_rupiah($maxPrice) }}
                                    <button wire:click="$set('minPrice', 0); $set('maxPrice', 50000000)"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
                                </span>
                            @endif
                        </div>
                    </section>
                @endif

                {{-- Kategori --}}
                <section class="mb-5">
                    <h4 class="font-display font-semibold text-[12px] text-ink mb-2.5 uppercase tracking-[0.12em]">Kategori</h4>
                    <div class="space-y-1 max-h-52 overflow-y-auto bg-paper/50 border border-black/5 rounded-xl p-2.5">
                        @foreach($categories as $category)
                            <label class="flex items-center gap-2.5 py-1 cursor-pointer">
                                <input type="checkbox" wire:model.live="selectedCategories" value="{{ $category->id }}"
                                       class="w-4 h-4 rounded border-black/20 text-emerald20-600 focus:ring-emerald20-500 focus:ring-2 focus:ring-offset-0 cursor-pointer">
                                <span class="text-[13px] text-ink/85 flex-1">{{ $category->name }}</span>
                                <span class="text-[11px] text-black/40 font-mono">{{ $category->products_count ?? 0 }}</span>
                            </label>
                        @endforeach
                    </div>
                </section>

                {{-- Harga --}}
                <section class="mb-2">
                    <h4 class="font-display font-semibold text-[12px] text-ink mb-2.5 uppercase tracking-[0.12em]">Rentang Harga</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-[11px] font-mono text-black/55">
                            <span>{{ format_rupiah($minPrice) }}</span>
                            <span>{{ format_rupiah($maxPrice) }}</span>
                        </div>
                        <input type="range" wire:model.live.debounce.500ms="maxPrice" class="range-slider w-full"
                               min="0" max="50000000" step="100000">
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <label class="block">
                                <span class="text-[10px] text-black/45 uppercase tracking-wider font-semibold">Min</span>
                                <input type="number" wire:model.live.debounce.500ms="minPrice" placeholder="0"
                                       class="w-full mt-0.5 px-2.5 py-2 bg-paper border border-black/10 rounded-lg text-[13px] font-mono text-ink focus:outline-none focus:border-emerald20-500 focus:bg-white">
                            </label>
                            <label class="block">
                                <span class="text-[10px] text-black/45 uppercase tracking-wider font-semibold">Max</span>
                                <input type="number" wire:model.live.debounce.500ms="maxPrice" placeholder="50.000.000"
                                       class="w-full mt-0.5 px-2.5 py-2 bg-paper border border-black/10 rounded-lg text-[13px] font-mono text-ink focus:outline-none focus:border-emerald20-500 focus:bg-white">
                            </label>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Footer --}}
            <div class="border-t border-black/5 p-4 bg-white shrink-0" style="padding-bottom: max(1rem, env(safe-area-inset-bottom));">
                <button wire:click="applyFilters"
                        class="w-full bg-emerald20-600 hover:bg-emerald20-700 active:bg-emerald20-800 text-white font-semibold text-[14px] py-3 rounded-xl transition-colors shadow-card">
                    Terapkan Filter
                </button>
            </div>
        </div>
    </div>

    <x-footer :categories="$categories" />

    <style>
        [x-cloak] { display: none !important; }

        /* Range slider — brand-themed */
        .range-slider {
            -webkit-appearance: none;
            appearance: none;
            height: 4px;
            background: #E5E5E5;
            border-radius: 999px;
            outline: none;
        }
        .range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            background: var(--color-emerald20-600);
            border: 2px solid #fff;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .range-slider::-moz-range-thumb {
            width: 18px;
            height: 18px;
            background: var(--color-emerald20-600);
            border: 2px solid #fff;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        /* Line clamp helper */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</div>
