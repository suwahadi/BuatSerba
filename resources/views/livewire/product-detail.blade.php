<div>
<div class="bg-white">
    <x-navbar />

    {{-- Breadcrumb — seragam dengan halaman katalog --}}
    <nav class="border-b border-black/5" aria-label="Breadcrumb">
        <div class="container-x py-2.5 overflow-x-auto">
            <ol class="flex items-center gap-1.5 text-[12px] whitespace-nowrap">
                <li><a href="/" class="text-black/55 hover:text-emerald20-600 transition-colors">Beranda</a></li>
                <li class="text-black/30">/</li>
                <li><a href="/catalog" class="text-black/55 hover:text-emerald20-600 transition-colors">Katalog</a></li>
                <li class="text-black/30">/</li>
                <li><a href="/{{ $product->category->slug }}" class="text-black/55 hover:text-emerald20-600 transition-colors">{{ $product->category->name }}</a></li>
                <li class="text-black/30">/</li>
                <li class="font-semibold text-ink truncate max-w-[160px] sm:max-w-[260px]">{{ Str::limit($product->name, 40) }}</li>
            </ol>
        </div>
    </nav>

    <div class="container-x py-5 sm:py-8 md:py-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-10">

            {{-- ============ Carousel ============ --}}
            <div class="space-y-3"
                 x-data="{
                     currentSlide: @entangle('currentCarouselIndex').live,
                     lightbox: false,
                     images: {{ json_encode($carouselImages) }},
                     productName: '{{ addslashes($product->name) }}',
                     totalSlides() { return this.images.length; },
                     imageUrl(p) { return p.startsWith('http') ? p : '/storage/' + p; },
                     next() { this.currentSlide = (this.currentSlide + 1) % this.totalSlides(); },
                     prev() { this.currentSlide = (this.currentSlide - 1 + this.totalSlides()) % this.totalSlides(); },
                     goToSlide(i) { this.currentSlide = parseInt(i); },
                     openLightbox() { this.lightbox = true; document.body.style.overflow = 'hidden'; },
                     closeLightbox() { this.lightbox = false; document.body.style.overflow = ''; },
                     touchStartX: 0, touchEndX: 0,
                     handleTouchStart(e) { this.touchStartX = e.touches[0].clientX; },
                     handleTouchMove(e) { this.touchEndX = e.touches[0].clientX; },
                     handleTouchEnd() {
                         const diff = this.touchStartX - this.touchEndX;
                         if (Math.abs(diff) > 50) { diff > 0 ? this.next() : this.prev(); }
                     }
                 }">

                {{-- Main image --}}
                <div class="relative aspect-square bg-paper rounded-2xl overflow-hidden shadow-card border border-black/[0.04] group"
                     @touchstart="handleTouchStart($event)"
                     @touchmove="handleTouchMove($event)"
                     @touchend="handleTouchEnd()"
                     @keydown.window.arrow-left.prevent="prev()"
                     @keydown.window.arrow-right.prevent="next()">

                    @foreach($carouselImages as $index => $image)
                        <div x-show="currentSlide === {{ $index }}"
                             x-transition:enter="transition-opacity duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             class="absolute inset-0 cursor-zoom-in"
                             @click="openLightbox()">
                            <img src="{{ image_url($image) }}"
                                 alt="{{ $product->name }}"
                                 title="{{ $product->name }}"
                                 class="w-full h-full object-cover select-none"
                                 draggable="false"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27800%27 height=%27800%27%3E%3Crect width=%27800%27 height=%27800%27 fill=%27%23f5f5f0%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 dominant-baseline=%27middle%27 text-anchor=%27middle%27 font-family=%27monospace%27 font-size=%2724px%27 fill=%27%239ca3af%27%3ETidak ada gambar%3C/text%3E%3C/svg%3E'">
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black/5 pointer-events-none">
                                <div class="bg-white/90 rounded-full p-2.5 sm:p-3 shadow-card">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-ink" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Prev/Next --}}
                    <template x-if="totalSlides() > 1">
                        <div>
                            <button @click.stop="prev()"
                                    class="absolute left-2 sm:left-3 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-9 sm:h-9 grid place-items-center rounded-full bg-white/90 hover:bg-white text-ink shadow-card opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity z-10"
                                    aria-label="Sebelumnya">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m15 6-6 6 6 6"/></svg>
                            </button>
                            <button @click.stop="next()"
                                    class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-9 sm:h-9 grid place-items-center rounded-full bg-white/90 hover:bg-white text-ink shadow-card opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity z-10"
                                    aria-label="Berikutnya">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m9 6 6 6-6 6"/></svg>
                            </button>
                        </div>
                    </template>

                    {{-- Indicator dots --}}
                    <template x-if="totalSlides() > 1">
                        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                            <template x-for="(image, index) in images" :key="index">
                                <button @click.stop="goToSlide(index)"
                                        class="h-1.5 rounded-full transition-all duration-300"
                                        :class="currentSlide === index ? 'bg-white w-6' : 'bg-white/60 hover:bg-white/85 w-1.5'"
                                        :aria-label="'Ke gambar ' + (index + 1)"></button>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Thumbnails --}}
                @if(count($carouselImages) > 1)
                    <div class="grid grid-cols-5 sm:grid-cols-6 gap-2">
                        @foreach($carouselImages as $index => $image)
                            <button @click="goToSlide({{ $index }})"
                                    :class="currentSlide === {{ $index }} ? 'ring-2 ring-emerald20-600 ring-offset-1' : 'opacity-70 hover:opacity-100'"
                                    class="aspect-square bg-paper rounded-lg overflow-hidden cursor-pointer transition-all focus:outline-none focus:ring-2 focus:ring-emerald20-600 border border-black/[0.04]">
                                <img src="{{ image_url($image) }}"
                                     alt="{{ $product->name }} - {{ $index + 1 }}"
                                     class="w-full h-full object-cover select-none"
                                     draggable="false"
                                     onerror="this.style.background='#F5F5F0'">
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- Lightbox --}}
                <template x-teleport="body">
                    <div x-show="lightbox" x-cloak
                         x-transition:enter="transition-opacity duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition-opacity duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/95 p-4"
                         @click.self="closeLightbox()"
                         @keydown.window.escape.prevent="if(lightbox) closeLightbox()"
                         @keydown.window.arrow-right.prevent="if(lightbox) next()"
                         @keydown.window.arrow-left.prevent="if(lightbox) prev()">
                        <div class="relative max-w-7xl max-h-full w-full flex items-center justify-center">
                            <template x-if="totalSlides() > 1">
                                <button @click.stop="prev()"
                                        class="absolute left-2 sm:-left-14 top-1/2 -translate-y-1/2 text-white/90 hover:text-white p-2 z-10"
                                        aria-label="Sebelumnya">
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                            </template>
                            <div class="relative w-full max-h-[90vh] flex items-center justify-center">
                                <template x-for="(image, index) in images" :key="index">
                                    <div x-show="currentSlide === index" class="flex items-center justify-center">
                                        <img :src="imageUrl(image)" :alt="productName" :title="productName"
                                             class="max-w-full max-h-[90vh] object-contain rounded-lg select-none"
                                             draggable="false">
                                    </div>
                                </template>
                            </div>
                            <template x-if="totalSlides() > 1">
                                <button @click.stop="next()"
                                        class="absolute right-2 sm:-right-14 top-1/2 -translate-y-1/2 text-white/90 hover:text-white p-2 z-10"
                                        aria-label="Berikutnya">
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </template>
                            <button @click="closeLightbox()"
                                    class="absolute -top-12 right-0 text-white/90 hover:text-white p-2"
                                    aria-label="Tutup">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 mb-3 text-center text-white/85">
                                <p class="text-[13px] sm:text-[14px] font-medium mb-0.5" x-text="productName"></p>
                                <p class="text-[11px] font-mono"><span x-text="currentSlide + 1"></span> / <span x-text="totalSlides()"></span></p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- ============ Product Info ============ --}}
            <div class="space-y-4 sm:space-y-5">

                {{-- Eyebrow + status --}}
                <div class="flex flex-wrap items-center gap-2">
                    @php
                        $detailFlash = $flashItem ?? null;
                    @endphp
                    @if($detailFlash)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-gradient-to-r from-sale to-tan5-500 shadow-chip">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2 3 14h7l-1 8 11-13h-7l1-7Z"/></svg>
                            Flash Sale · Sisa {{ $detailFlash->remaining_stock }}
                        </span>
                    @endif
                    @if($product->is_featured)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-violet10-500 shadow-chip">Pilihan</span>
                    @endif
                    <span class="text-[11px] font-semibold uppercase tracking-wider {{ $selectedSku && $selectedSku->stock_quantity > 0 ? 'text-emerald20-600' : 'text-sale' }}">
                        {{ $selectedSku && $selectedSku->stock_quantity > 0 ? 'Tersedia' : 'Stok Habis' }}
                    </span>
                </div>

                {{-- Title --}}
                <div>
                    <h1 class="font-display font-extrabold text-[20px] md:text-[26px] leading-tight tracking-tight text-ink">{{ $product->name }}</h1>
                    @if($selectedSku)
                        <p class="text-[11px] sm:text-[12px] text-black/45 mt-1 font-mono">SKU: {{ $selectedSku->sku }}</p>
                    @endif
                </div>

                {{-- Rating row --}}
                <div class="flex items-center gap-3 text-[12px] text-black/55">
                    <div class="flex items-center gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($this->averageRating))
                                <svg class="w-4 h-4 text-tan5-500 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @else
                                <svg class="w-4 h-4 text-black/15 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endif
                        @endfor
                        <span class="ml-1 font-semibold text-ink">{{ number_format($this->averageRating, 1) }}</span>
                    </div>
                    <span class="opacity-30">·</span>
                    <span>{{ number_format($product->view_count) }} dilihat</span>
                </div>

                {{-- Price block --}}
                @if($selectedSku)
                    @php
                        $detailDisplayPrice = $detailFlash ? (float) $detailFlash->flash_price : (float) $selectedSku->selling_price;
                        $detailBasePrice = $detailFlash
                            ? (float) ($detailFlash->original_price_snapshot ?: $selectedSku->selling_price)
                            : (float) $selectedSku->base_price;
                        $detailDiscountPct = $detailFlash
                            ? $detailFlash->discount_percentage
                            : ($detailBasePrice > $detailDisplayPrice ? discount_percentage($detailBasePrice, $detailDisplayPrice) : 0);
                    @endphp
                    <div class="bg-paper/60 border border-black/5 rounded-2xl p-4 sm:p-5">
                        <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                            <span class="font-mono font-extrabold text-emerald20-700 text-[26px] sm:text-[30px] leading-none">{{ format_rupiah($detailDisplayPrice) }}</span>
                            @if($detailBasePrice > $detailDisplayPrice)
                                <span class="font-mono text-[14px] sm:text-[15px] strike text-black/40">{{ format_rupiah($detailBasePrice) }}</span>
                                @if($detailDiscountPct > 0)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[11px] font-extrabold bg-sale/10 text-sale border border-sale/20">-{{ $detailDiscountPct }}%</span>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Variants --}}
                @if(count($availableVariants) > 0)
                    @foreach($availableVariants as $attributeName => $values)
                        <div>
                            <h3 class="font-display font-semibold text-[12px] text-ink mb-2 uppercase tracking-[0.12em]">{{ $attributeName }}</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($values as $value)
                                    @php $isSel = isset($selectedVariants[$attributeName]) && $selectedVariants[$attributeName] == $value; @endphp
                                    <button wire:click="selectVariant('{{ $attributeName }}', '{{ $value }}')"
                                            class="px-3.5 py-1.5 text-[13px] border rounded-lg font-medium transition-all {{ $isSel ? 'border-emerald20-600 bg-emerald20-50 text-emerald20-700' : 'border-black/10 bg-white text-ink/85 hover:border-emerald20-500 hover:text-emerald20-600' }}">
                                        {{ $value }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Quantity + actions --}}
                @if($selectedSku && $selectedSku->stock_quantity > 0)
                    <div>
                        <h3 class="font-display font-semibold text-[12px] text-ink mb-2 uppercase tracking-[0.12em]">Jumlah</h3>
                        <div class="flex items-center gap-3">
                            <div class="inline-flex items-center bg-white border border-black/10 rounded-xl overflow-hidden">
                                <button wire:click="decrementQuantity"
                                        class="px-3 py-2 hover:bg-paper active:bg-paper/80 transition-colors text-ink/70 border-r border-black/5"
                                        aria-label="Kurangi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M20 12H4"/></svg>
                                </button>
                                <input type="number" wire:model.live="quantity" min="1" max="{{ $selectedSku->stock_quantity }}"
                                       class="w-12 sm:w-14 text-center py-2 text-[14px] font-mono font-bold text-ink bg-transparent focus:outline-none border-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                <button wire:click="incrementQuantity"
                                        class="px-3 py-2 hover:bg-paper active:bg-paper/80 transition-colors text-ink/70 border-l border-black/5"
                                        aria-label="Tambah">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                            <span class="text-[12px] text-black/55">
                                <span class="font-mono font-semibold text-ink">{{ number_format($selectedSku->stock_quantity) }}</span> tersedia
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-[1fr_auto] sm:grid-cols-[1fr_1fr_auto] gap-2 pt-1">
                        <button wire:click="addToCart"
                                class="inline-flex items-center justify-center gap-2 bg-white border border-emerald20-600 text-emerald20-700 hover:bg-emerald20-50 active:bg-emerald20-100 py-2.5 sm:py-3 rounded-xl text-[13px] sm:text-[14px] font-semibold transition-colors shadow-card">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 6h13"/><circle cx="9" cy="20" r="1.5"/><circle cx="17" cy="20" r="1.5"/></svg>
                            <span>Tambah ke Keranjang</span>
                        </button>
                        <button wire:click="buyNow"
                                class="col-span-2 sm:col-span-1 inline-flex items-center justify-center gap-2 grad-violet-emerald hover:opacity-95 active:opacity-90 text-white py-2.5 sm:py-3 rounded-xl text-[13px] sm:text-[14px] font-semibold transition-opacity shadow-card order-3 sm:order-2">
                            <span>Beli Sekarang</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 12h14"/><path stroke-linecap="round" d="m13 5 7 7-7 7"/></svg>
                        </button>
                        <livewire:components.wishlist-button :sku-id="$selectedSku->id" variant="detail" :key="'wb-detail-'.$selectedSku->id" />
                    </div>
                @else
                    <div class="bg-sale/5 border border-sale/20 rounded-xl p-3.5 text-[13px] text-sale font-semibold">
                        Produk ini sedang tidak tersedia
                    </div>
                @endif

                {{-- Features --}}
                @if($product->features)
                    <div class="bg-paper/60 border border-black/5 rounded-2xl p-4">
                        <h3 class="font-display font-semibold text-[12px] text-ink mb-2.5 uppercase tracking-[0.12em]">Keunggulan</h3>
                        <ul class="space-y-1.5 text-[13px] text-ink/80">
                            @foreach($product->features as $feature)
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-emerald20-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        {{-- ============ Tabs ============ --}}
        <div class="mt-8 sm:mt-12 bg-white rounded-2xl shadow-card border border-black/[0.04] overflow-hidden">
            <div class="border-b border-black/5 overflow-x-auto">
                <nav class="flex gap-5 sm:gap-8 px-4 sm:px-6 min-w-max">
                    <button wire:click="setActiveTab('description')"
                            class="py-3 sm:py-4 border-b-2 font-display font-semibold text-[13px] sm:text-[14px] whitespace-nowrap transition-colors {{ $activeTab === 'description' ? 'border-emerald20-600 text-emerald20-700' : 'border-transparent text-black/55 hover:text-ink' }}">
                        Deskripsi
                    </button>
                    <button wire:click="setActiveTab('reviews')"
                            class="py-3 sm:py-4 border-b-2 font-display font-semibold text-[13px] sm:text-[14px] whitespace-nowrap transition-colors {{ $activeTab === 'reviews' ? 'border-emerald20-600 text-emerald20-700' : 'border-transparent text-black/55 hover:text-ink' }}">
                        Ulasan <span class="font-mono text-[11px] text-black/40">({{ $this->reviewCount }})</span>
                    </button>
                </nav>
            </div>

            <div class="p-4 sm:p-6">
                {{-- Description --}}
                <div class="{{ $activeTab === 'description' ? '' : 'hidden' }}">
                    <div class="product-description text-ink/80 text-[13px] sm:text-[14px] leading-relaxed">
                        {!! $product->description !!}
                    </div>
                </div>

                {{-- Reviews --}}
                <div class="{{ $activeTab === 'reviews' ? '' : 'hidden' }}">
                    <div class="space-y-5">
                        @forelse($product->reviews as $review)
                            <div class="border-b border-black/5 pb-5 last:border-0 last:pb-0">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-paper border border-black/[0.04] grid place-items-center shrink-0">
                                            <span class="text-[12px] font-bold text-ink/70">{{ substr($review->user->name ?? 'U', 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <h4 class="text-[13px] font-semibold text-ink leading-tight">{{ $review->user->name ?? 'Pengguna' }}</h4>
                                            <span class="text-[11px] text-black/45 font-mono">{{ $review->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-0.5 shrink-0">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-tan5-500' : 'text-black/10' }} fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-[13px] text-ink/85 leading-relaxed">{{ $review->review }}</p>

                                @if(!empty($review->images) && is_array($review->images))
                                    @php $imageUrls = collect($review->images)->map(fn($img) => Storage::url($img))->toArray(); @endphp
                                    <div class="mt-3 flex gap-2 overflow-x-auto pb-1 no-scrollbar"
                                         x-data="{
                                             lightbox: false,
                                             images: {{ json_encode($imageUrls) }},
                                             currentIndex: 0,
                                             get currentImage() { return this.images[this.currentIndex]; },
                                             next() { this.currentIndex = (this.currentIndex + 1) % this.images.length; },
                                             prev() { this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length; }
                                         }">
                                        @foreach($review->images as $index => $image)
                                            <div class="relative w-16 h-16 sm:w-20 sm:h-20 flex-shrink-0 bg-paper rounded-lg overflow-hidden border border-black/5 cursor-pointer hover:opacity-90"
                                                 @click="currentIndex = {{ $index }}; lightbox = true">
                                                <img src="{{ Storage::url($image) }}" class="w-full h-full object-cover">
                                            </div>
                                        @endforeach

                                        <div x-show="lightbox" x-cloak
                                             class="fixed inset-0 z-50 flex items-center justify-center bg-black/95 p-4"
                                             x-transition.opacity
                                             @click.self="lightbox = false"
                                             @keydown.window.escape="lightbox = false"
                                             @keydown.window.arrow-right="if(lightbox) next()"
                                             @keydown.window.arrow-left="if(lightbox) prev()">
                                            <div class="relative max-w-4xl max-h-full flex items-center justify-center w-full">
                                                <button x-show="images.length > 1" @click.stop="prev()"
                                                        class="absolute left-2 sm:-left-14 top-1/2 -translate-y-1/2 text-white/90 hover:text-white p-2 z-10">
                                                    <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                                </button>
                                                <img :src="currentImage" class="max-w-full max-h-[85vh] object-contain rounded-lg select-none">
                                                <button x-show="images.length > 1" @click.stop="next()"
                                                        class="absolute right-2 sm:-right-14 top-1/2 -translate-y-1/2 text-white/90 hover:text-white p-2 z-10">
                                                    <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                                </button>
                                                <button @click="lightbox = false" class="absolute -top-12 right-0 text-white/90 hover:text-white p-2">
                                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                                <div x-show="images.length > 1" class="absolute -bottom-9 left-0 right-0 text-center text-white/85 text-[12px] font-mono">
                                                    <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <p class="text-[13px] text-black/50">Belum ada ulasan untuk produk ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ Related Products ============ --}}
        @if($relatedProducts->count() > 0)
            <section class="mt-10 sm:mt-14" aria-label="Produk Terkait">
                <div class="flex items-end justify-between mb-4 md:mb-5">
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.2em] text-violet10-500 font-semibold">Mungkin Juga Suka</p>
                        <h2 class="font-display font-extrabold text-[20px] md:text-[26px] tracking-tight text-ink mt-0.5">Produk Terkait</h2>
                    </div>
                    <a href="/catalog?category={{ $product->category_id }}" class="inline-flex items-center gap-1.5 text-[13px] text-emerald20-700 font-semibold hover:underline shrink-0">
                        <span class="hidden sm:inline">Lihat Semua</span>
                        <span class="sm:hidden">Semua</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14"/><path d="m13 5 7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                    @foreach($relatedProducts as $relatedProduct)
                        @php
                            $relatedSku = $relatedProduct->skus->first();
                            $rPrice = $relatedSku?->selling_price ?? 0;
                            $rBase = $relatedSku?->base_price ?? 0;
                            $rHasDiscount = $relatedSku && $rBase > $rPrice;
                            $rDiscountPct = $rHasDiscount ? discount_percentage($rBase, $rPrice) : 0;
                            $rPhN = ($loop->index % 6) + 1;
                            if ($rPrice <= 25000) { $rTierCls = 'tier-25'; $rTierLabel = 'Serba 25'; }
                            elseif ($rPrice <= 35000) { $rTierCls = 'tier-35'; $rTierLabel = 'Serba 35'; }
                            elseif ($rPrice <= 45000) { $rTierCls = 'tier-45'; $rTierLabel = 'Serba 45'; }
                            else { $rTierCls = 'tier-mix'; $rTierLabel = 'Serba-Serbi'; }
                            $rIsNew = $relatedProduct->created_at && $relatedProduct->created_at->diffInDays(now()) <= 7;
                        @endphp
                        <article class="pcard relative bg-white rounded-2xl shadow-card overflow-hidden border border-black/[0.04] focus-within:ring-2 focus-within:ring-emerald20-300">
                            <a href="{{ route('product.detail', $relatedProduct->slug) }}" class="block focus:outline-none">
                                <div class="relative ph-{{ $rPhN }} aspect-square overflow-hidden">
                                    <img loading="lazy" class="pimg w-full h-full object-cover" src="{{ image_url($relatedProduct->main_image) }}" alt="{{ $relatedProduct->name }}" onerror="this.src='/images/placeholder.jpg'">
                                    @if($rHasDiscount && $rDiscountPct > 0)
                                        <span class="absolute top-2 right-2 bg-sale text-white text-[11px] font-extrabold px-1.5 py-0.5 rounded-md shadow-chip">-{{ $rDiscountPct }}%</span>
                                    @endif
                                    @if($relatedProduct->is_featured)
                                        <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-violet10-500 shadow-chip">Pilihan</span>
                                    @elseif($rIsNew)
                                        <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-emerald20-600 shadow-chip">Baru</span>
                                    @endif
                                </div>
                                <div class="p-3 md:p-4">
                                    @if($rPrice > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $rTierCls }}">{{ $rTierLabel }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tier-mix">{{ $relatedProduct->category->name ?? 'Kategori' }}</span>
                                    @endif
                                    <h3 class="mt-1.5 text-[13px] md:text-[14px] font-medium leading-snug text-ink line-clamp-2 min-h-[2.6em]">{{ $relatedProduct->name }}</h3>
                                    @if($relatedSku)
                                        <div class="mt-1.5">
                                            <span class="font-mono font-extrabold text-emerald20-700 text-[15px] md:text-[16px] whitespace-nowrap">{{ format_rupiah($rPrice) }}</span>
                                        </div>
                                        @if($rHasDiscount)
                                            <div class="text-[11px] md:text-[12px] strike text-black/45 whitespace-nowrap">{{ format_rupiah($rBase) }}</div>
                                        @endif
                                    @endif
                                    <div class="mt-2 flex items-center gap-2 text-[11px] text-black/55">
                                        <span class="inline-flex items-center gap-0.5 text-emerald20-600 font-semibold">
                                            <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            4.8
                                        </span>
                                        <span class="opacity-30">·</span>
                                        <span>Terjual {{ $relatedProduct->view_count > 0 ? number_format($relatedProduct->view_count) : 0 }}</span>
                                    </div>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <x-footer />

    @if(session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-4 right-4 bg-sale text-white px-5 py-3 rounded-xl shadow-card z-50 text-[13px] font-semibold">
            {{ session('error') }}
        </div>
    @endif

    {{-- Cart notification (kept functional, restyled with brand tokens) --}}
    <div x-data="{
            open: false, productName: '', quantity: 0, price: 0,
            show(productName, quantity, price) { this.open = true; this.productName = productName; this.quantity = quantity; this.price = price; },
            hide() { this.open = false; },
            goToCart() { window.location.href = '/cart'; }
         }"
         x-on:show-cart-notification.window="show($event.detail.productName, $event.detail.quantity, $event.detail.price)"
         x-show="open" x-cloak
         x-transition.opacity.duration.200ms
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
         @click.self="hide()">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full text-center shadow-cardHover border border-black/[0.04]"
             x-transition:enter="transition transform duration-300 ease-out"
             x-transition:enter-start="scale-95 opacity-0"
             x-transition:enter-end="scale-100 opacity-100">
            <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-emerald20-100 grid place-items-center">
                <svg class="w-7 h-7 text-emerald20-700" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 class="font-display font-bold text-[16px] text-ink">Berhasil ditambahkan!</h3>
            <p class="text-[13px] text-black/55 mt-1">Produk telah masuk ke keranjang belanja Anda</p>
            <div class="flex gap-2 mt-5">
                <button @click="hide()" class="flex-1 py-2.5 rounded-xl border border-black/10 text-ink/75 hover:border-emerald20-500 hover:text-emerald20-700 text-[13px] font-semibold transition-colors">
                    Lanjut Belanja
                </button>
                <button @click="goToCart()" class="flex-1 py-2.5 rounded-xl grad-violet-emerald hover:opacity-95 text-white text-[13px] font-semibold transition-opacity shadow-card">
                    Lihat Keranjang
                </button>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }

        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

        /* Product description typography (rich-text from CMS) */
        .product-description p { margin-bottom: 0.9em; line-height: 1.65; }
        .product-description p:last-child { margin-bottom: 0; }
        .product-description ul { list-style-type: disc; padding-left: 1.4em; margin-bottom: 0.9em; }
        .product-description ol { list-style-type: decimal; padding-left: 1.4em; margin-bottom: 0.9em; }
        .product-description li { margin-bottom: 0.2em; }
        .product-description strong, .product-description b { font-weight: 700; color: var(--color-ink); }
        .product-description em, .product-description i { font-style: italic; }
        .product-description h1 { font-family: var(--font-display); font-size: 1.35em; font-weight: 800; margin-top: 1.2em; margin-bottom: 0.5em; color: var(--color-ink); letter-spacing: -0.01em; }
        .product-description h2 { font-family: var(--font-display); font-size: 1.2em; font-weight: 700; margin-top: 1.1em; margin-bottom: 0.45em; color: var(--color-ink); }
        .product-description h3 { font-family: var(--font-display); font-size: 1.05em; font-weight: 700; margin-top: 1em; margin-bottom: 0.4em; color: var(--color-ink); }
        .product-description blockquote { border-left: 3px solid var(--color-emerald20-200); padding-left: 0.9em; margin: 0.9em 0; color: var(--color-tan5-700); font-style: italic; }
        .product-description img { max-width: 100%; height: auto; border-radius: 0.75rem; margin: 0.9em 0; }
        .product-description a { color: var(--color-emerald20-700); text-decoration: underline; text-underline-offset: 2px; }
        .product-description a:hover { color: var(--color-emerald20-800); }
    </style>
</div>
</div>
