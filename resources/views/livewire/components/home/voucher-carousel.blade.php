{{--
    Promo / Voucher carousel — home-component partial.
    Expects $vouchers (Collection of App\Models\Voucher).
    Alpine drives the slider + popup; tokens align with the storefront design system.
--}}
@php
    $vouchers = $vouchers ?? collect();
@endphp
@if($vouchers->count() > 0)
<section aria-label="Promo Spesial" class="container-x mt-10 md:mt-16">
    <div class="flex items-end justify-between mb-3 md:mb-4">
        <div>
            <p class="text-[12px] uppercase tracking-[0.2em] text-tan5-600 font-semibold">Jangan Sampai Terlewat</p>
            <h2 class="font-display font-extrabold text-[22px] md:text-[28px] tracking-tight mt-1">Promo Spesial</h2>
        </div>
    </div>

    <div wire:ignore
         x-data="{
            activeSlide: 0,
            originalTotal: {{ $vouchers->count() }},
            visible: 3,
            autoSlideInterval: null,
            transitioning: true,
            popupOpen: false,
            selectedVoucher: null,
            copied: false,
            touchStartX: 0,
            touchEndX: 0,

            init() {
                this.updateVisible();
                window.addEventListener('resize', () => this.updateVisible());
                this.startAutoSlide();
            },

            updateVisible() {
                if (window.innerWidth >= 1024) this.visible = 3;
                else if (window.innerWidth >= 768) this.visible = 2;
                else this.visible = 1;
            },

            next() {
                this.transitioning = true;
                this.activeSlide++;

                if (this.activeSlide === this.originalTotal) {
                    setTimeout(() => {
                        this.transitioning = false;
                        this.activeSlide = 0;
                    }, 500);
                }
            },

            prev() {
                if (this.activeSlide === 0) {
                    this.transitioning = false;
                    this.activeSlide = this.originalTotal;
                    setTimeout(() => {
                        this.transitioning = true;
                        this.activeSlide--;
                    }, 50);
                } else {
                    this.transitioning = true;
                    this.activeSlide--;
                }
            },

            startAutoSlide() {
                this.stopAutoSlide();
                this.autoSlideInterval = setInterval(() => {
                    this.next();
                }, 4000);
            },

            stopAutoSlide() {
                if (this.autoSlideInterval) {
                    clearInterval(this.autoSlideInterval);
                    this.autoSlideInterval = null;
                }
            },

            handleTouchStart(e) {
                this.touchStartX = e.changedTouches[0].screenX;
            },

            handleTouchEnd(e) {
                this.touchEndX = e.changedTouches[0].screenX;
                const diff = this.touchStartX - this.touchEndX;
                const threshold = 50;
                if (Math.abs(diff) > threshold) {
                    if (diff > 0) this.next();
                    else this.prev();
                }
            },

            openVoucherPopup(el) {
                const target = el.currentTarget;
                this.selectedVoucher = {
                    name: target.dataset.voucherName || '',
                    code: target.dataset.voucherCode || '',
                    type: target.dataset.voucherType || 'number',
                    amount: parseFloat(target.dataset.voucherAmount) || 0,
                    minSpend: parseFloat(target.dataset.voucherMinSpend) || 0,
                    validEnd: target.dataset.voucherValidEnd || '',
                    isFreeShipment: target.dataset.voucherFreeShipment === '1'
                };
                this.copied = false;
                this.popupOpen = true;
                this.stopAutoSlide();
            },

            closePopup() {
                this.popupOpen = false;
                this.selectedVoucher = null;
                this.startAutoSlide();
            },

            async copyCode() {
                if (!this.selectedVoucher?.code) return;
                try {
                    await navigator.clipboard.writeText(this.selectedVoucher.code);
                    this.copied = true;
                    setTimeout(() => { this.copied = false; }, 2000);
                } catch (err) {
                    console.error('Copy failed:', err);
                }
            },

            formatAmount() {
                if (!this.selectedVoucher) return '';
                if (this.selectedVoucher.type === 'percentage') {
                    return this.selectedVoucher.amount + '%';
                }
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(this.selectedVoucher.amount);
            },

            formatMinSpend() {
                if (!this.selectedVoucher || !this.selectedVoucher.minSpend) return null;
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(this.selectedVoucher.minSpend);
            },

            formatValidEnd() {
                if (!this.selectedVoucher?.validEnd) return null;
                try {
                    const d = new Date(this.selectedVoucher.validEnd);
                    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                } catch {
                    return this.selectedVoucher.validEnd;
                }
            }
         }"
         @mouseenter="stopAutoSlide"
         @mouseleave="startAutoSlide"
         class="relative group">

        {{-- Carousel Track --}}
        <div class="overflow-hidden touch-pan-y rounded-2xl"
             @touchstart="handleTouchStart($event)"
             @touchend="handleTouchEnd($event)">
            <div class="flex"
                 :class="{ 'transition-transform duration-500 ease-in-out': transitioning }"
                 :style="`transform: translateX(-${activeSlide * (100 / visible)}%)`">

                @php
                    $allVouchers = $vouchers->concat($vouchers->take(3));
                @endphp

                @foreach($allVouchers as $voucher)
                    @php
                        $minSpend = $voucher->min_spend ? (float) $voucher->min_spend : 0;
                        $validEnd = $voucher->valid_end?->format('Y-m-d\TH:i:s') ?? '';
                    @endphp
                    <div class="flex-shrink-0 px-2"
                         :style="`width: ${100/visible}%`">
                        <div class="bg-paper rounded-xl overflow-hidden shadow-card hover:shadow-pop transition-shadow aspect-[16/6] md:aspect-[21/9] cursor-pointer"
                             @click="openVoucherPopup($event)"
                             data-voucher-name="{{ e($voucher->voucher_name) }}"
                             data-voucher-code="{{ e($voucher->voucher_code) }}"
                             data-voucher-type="{{ e($voucher->type) }}"
                             data-voucher-amount="{{ (float) $voucher->amount }}"
                             data-voucher-min-spend="{{ $minSpend }}"
                             data-voucher-valid-end="{{ $validEnd }}"
                             data-voucher-free-shipment="{{ $voucher->is_free_shipment ? '1' : '0' }}">
                            <img src="{{ image_url($voucher->image) }}"
                                 alt="{{ $voucher->voucher_name }}"
                                 class="w-full h-full object-cover pointer-events-none">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Prev --}}
        <button @click="prev"
                class="absolute left-0 top-1/2 -translate-y-1/2 -ml-2 lg:-ml-4 bg-paper hover:bg-tan5-50 text-ink p-2 rounded-full shadow-card border border-black/5 opacity-0 group-hover:opacity-100 transition-opacity z-10 touch-none"
                aria-label="Promo sebelumnya">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>

        {{-- Next --}}
        <button @click="next"
                class="absolute right-0 top-1/2 -translate-y-1/2 -mr-2 lg:-mr-4 bg-paper hover:bg-tan5-50 text-ink p-2 rounded-full shadow-card border border-black/5 opacity-0 group-hover:opacity-100 transition-opacity z-10 touch-none"
                aria-label="Promo berikutnya">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        {{-- Voucher Popup --}}
        <template x-teleport="body">
            <div x-show="popupOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-ink/60 backdrop-blur-sm"
                @click="closePopup"
                @keydown.escape.window="closePopup"
                style="display: none;">

                <div @click.stop
                     class="relative w-full max-w-md bg-paper rounded-2xl shadow-pop overflow-hidden"
                     x-show="selectedVoucher"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">

                    {{-- Header --}}
                    <div class="grad-violet-emerald banknote-grain px-4 py-5 sm:px-6 sm:py-6 text-white">
                        <h3 class="font-display font-extrabold text-lg sm:text-xl tracking-tight" x-text="selectedVoucher?.name || 'Voucher'"></h3>
                        <p class="mt-1 text-[12px] uppercase tracking-wider text-tan5-200 font-bold">Spesial hanya untuk kamu</p>
                    </div>

                    {{-- Content --}}
                    <div class="p-4 sm:p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                            <div class="flex-1 min-w-0">
                                <p class="font-mono font-extrabold text-2xl sm:text-3xl text-sale" x-text="formatAmount()"></p>
                                <p class="mt-1 text-sm text-ink/60" x-show="formatMinSpend()" x-text="'Min. belanja ' + formatMinSpend()"></p>
                            </div>
                            <div class="flex-1 min-w-0 space-y-1 text-sm text-ink/60">
                                <p x-show="formatValidEnd()" x-text="'Berlaku sampai ' + formatValidEnd()"></p>
                                <p x-show="selectedVoucher?.isFreeShipment" class="text-emerald20-600 font-semibold">Gratis ongkir</p>
                            </div>
                        </div>

                        {{-- Copy Code --}}
                        <div class="pt-2">
                            <p class="text-[11px] uppercase tracking-wider text-ink/60 font-bold mb-2">Kode Voucher</p>
                            <button type="button"
                                    @click="copyCode"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-lg border-2 border-dashed border-tan5-300 bg-tan5-50 hover:bg-tan5-100 hover:border-tan5-400 transition-colors text-sm font-mono font-bold text-ink">
                                <span x-show="!copied" x-text="selectedVoucher?.code || ''"></span>
                                <span x-show="copied" class="text-emerald20-600 font-bold">Tersalin!</span>
                                <svg x-show="!copied" class="w-4 h-4 text-tan5-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <svg x-show="copied" class="w-4 h-4 text-emerald20-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Close --}}
                    <button @click="closePopup"
                            class="absolute top-3 right-3 p-2 rounded-full text-white/90 hover:bg-white/15 transition-colors focus:outline-none"
                            aria-label="Tutup">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>
</section>
@endif
