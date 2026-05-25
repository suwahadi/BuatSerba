<div>
    @php
        $flashSale = $flashSale ?? null;
        $items = $items ?? collect();
        $itemCount = $items->count();
    @endphp

    @if($flashSale && $flashSale->isLive() && $itemCount > 0)
    <section id="flash-sale" aria-labelledby="flash-h" class="container-x mt-10 md:mt-16">
        <div class="grad-violet-emerald relative overflow-hidden rounded-2xl shadow-card text-white">
            <div class="shimmer absolute inset-0 pointer-events-none"></div>
            <span class="absolute top-6 right-12 text-yellow-200 twinkle">✦</span>
            <span class="absolute top-12 right-1/3 text-tan5-200 twinkle" style="animation-delay:.6s">✦</span>
            <span class="absolute bottom-8 left-1/4 text-yellow-100 twinkle" style="animation-delay:1.2s">✦</span>
            <span class="absolute top-3 left-10 text-violet10-100 twinkle" style="animation-delay:.3s">✦</span>

            <div class="px-5 md:px-8 pt-6 md:pt-8 pb-3 flex flex-wrap items-center gap-x-4 gap-y-3 relative">
                <div class="flex items-center gap-3">
                    <span class="grid place-items-center w-11 h-11 rounded-xl bg-white/15 backdrop-blur shrink-0">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" class="text-tan5-200" aria-hidden="true"><path d="M13 2 3 14h7l-1 8 11-13h-7l1-7Z"/></svg>
                    </span>
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.2em] text-tan5-200 font-bold">{{ $flashSale->tagline ?: 'Hari Ini Saja' }}</p>
                        <h2 id="flash-h" class="font-display font-extrabold text-[22px] md:text-[28px] leading-[1.2] py-0.5">{{ $flashSale->name ?: 'Flash Sale' }}</h2>
                    </div>
                </div>
                <div class="flex items-center gap-2 ml-auto flex-wrap">
                    <span class="text-[12px] text-white/70 hidden sm:inline">Berakhir dalam</span>
                    <div id="flash-countdown"
                         data-remaining="{{ $flashSale->remainingSeconds() }}"
                         class="flex gap-1.5 font-mono font-extrabold">
                        <span data-cd="h" class="bg-black/30 backdrop-blur rounded-md px-2.5 py-1.5 text-[16px] md:text-[18px]">00</span>
                        <span class="self-center">:</span>
                        <span data-cd="m" class="bg-black/30 backdrop-blur rounded-md px-2.5 py-1.5 text-[16px] md:text-[18px]">00</span>
                        <span class="self-center">:</span>
                        <span data-cd="s" class="bg-black/30 backdrop-blur rounded-md px-2.5 py-1.5 text-[16px] md:text-[18px]">00</span>
                    </div>
                    <a href="{{ route('catalog') }}?flash=1" class="hidden md:inline-flex h-10 items-center px-4 rounded-lg bg-tan5-300 text-tan5-800 font-bold text-sm hover:bg-tan5-200">Lihat Semua</a>
                </div>
            </div>

            <div class="relative pb-7">
                @if($itemCount > 1)
                    <button type="button" id="flash-prev"
                            class="hidden sm:grid place-items-center absolute left-2 md:left-3 top-1/2 -translate-y-1/2 w-9 h-9 md:w-10 md:h-10 rounded-full bg-white/95 hover:bg-white shadow-card text-ink z-10"
                            aria-label="Item sebelumnya">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"/></svg>
                    </button>
                    <button type="button" id="flash-next"
                            class="hidden sm:grid place-items-center absolute right-2 md:right-3 top-1/2 -translate-y-1/2 w-9 h-9 md:w-10 md:h-10 rounded-full bg-white/95 hover:bg-white shadow-card text-ink z-10"
                            aria-label="Item berikutnya">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg>
                    </button>
                @endif

                <div id="flash-viewport" class="overflow-hidden px-5 md:px-8">
                    <div id="flash-track" class="flex gap-3 md:gap-4 will-change-transform" style="transform: translateX(0);">
                        @foreach($items as $item)
                            @php
                                $product = $item->sku->product;
                                $stockLimit = max(1, (int) $item->stock_limit);
                                $remaining = max(0, $stockLimit - (int) $item->sold_count);
                                $progressPct = min(99, ((int) $item->sold_count / $stockLimit) * 100);
                                $hasFlashImage = $product?->main_image;
                            @endphp
                            <article data-flash-card wire:key="flash-item-{{ $item->id }}" class="snap-start shrink-0 w-[160px] md:w-[200px] bg-white rounded-xl text-ink shadow-card overflow-hidden">
                                <a href="{{ route('product.detail', $product->slug) }}?sku={{ $item->sku_id }}" class="block">
                                    <div class="relative aspect-square bg-paper">
                                        @if($hasFlashImage)
                                            <img src="{{ product_image($product, 'medium') }}"
                                                 alt="{{ $product->name }}"
                                                 class="absolute inset-0 w-full h-full object-cover"
                                                 loading="lazy"
                                                 draggable="false">
                                        @endif
                                        @if($item->discount_percentage > 0)
                                            <span class="absolute top-1.5 right-1.5 bg-sale text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded-md">-{{ $item->discount_percentage }}%</span>
                                        @endif
                                    </div>
                                    <div class="p-2.5">
                                        <h4 class="text-[12px] font-medium line-clamp-2 min-h-[2.4em] leading-tight">{{ $product->name }}</h4>
                                        <div class="mt-1.5 font-mono font-extrabold text-emerald20-700 text-[14px] whitespace-nowrap">{{ format_rupiah((int) $item->flash_price) }}</div>
                                        @if($item->original_price_snapshot && $item->original_price_snapshot > $item->flash_price)
                                            <div class="text-[10.5px] strike text-black/40 whitespace-nowrap">{{ format_rupiah((int) $item->original_price_snapshot) }}</div>
                                        @endif
                                        <div class="mt-2">
                                            <div class="h-1.5 rounded-full bg-paper overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-sale to-tan5-400" style="width: {{ $progressPct }}%"></div>
                                            </div>
                                            <p class="text-[10.5px] mt-1 text-sale font-semibold">Tersisa {{ $remaining }}</p>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        @endforeach

                        {{-- Clones for seamless infinite loop --}}
                        @foreach($items as $item)
                            @php
                                $product = $item->sku->product;
                                $stockLimit = max(1, (int) $item->stock_limit);
                                $remaining = max(0, $stockLimit - (int) $item->sold_count);
                                $progressPct = min(99, ((int) $item->sold_count / $stockLimit) * 100);
                                $hasFlashImage = $product?->main_image;
                            @endphp
                            <article data-flash-card-clone aria-hidden="true" class="snap-start shrink-0 w-[160px] md:w-[200px] bg-white rounded-xl text-ink shadow-card overflow-hidden">
                                <a href="{{ route('product.detail', $product->slug) }}?sku={{ $item->sku_id }}" class="block" tabindex="-1">
                                    <div class="relative aspect-square bg-paper">
                                        @if($hasFlashImage)
                                            <img src="{{ product_image($product, 'medium') }}"
                                                 alt=""
                                                 class="absolute inset-0 w-full h-full object-cover"
                                                 loading="lazy"
                                                 draggable="false">
                                        @endif
                                        @if($item->discount_percentage > 0)
                                            <span class="absolute top-1.5 right-1.5 bg-sale text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded-md">-{{ $item->discount_percentage }}%</span>
                                        @endif
                                    </div>
                                    <div class="p-2.5">
                                        <h4 class="text-[12px] font-medium line-clamp-2 min-h-[2.4em] leading-tight">{{ $product->name }}</h4>
                                        <div class="mt-1.5 font-mono font-extrabold text-emerald20-700 text-[14px] whitespace-nowrap">{{ format_rupiah((int) $item->flash_price) }}</div>
                                        @if($item->original_price_snapshot && $item->original_price_snapshot > $item->flash_price)
                                            <div class="text-[10.5px] strike text-black/40 whitespace-nowrap">{{ format_rupiah((int) $item->original_price_snapshot) }}</div>
                                        @endif
                                        <div class="mt-2">
                                            <div class="h-1.5 rounded-full bg-paper overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-sale to-tan5-400" style="width: {{ $progressPct }}%"></div>
                                            </div>
                                            <p class="text-[10.5px] mt-1 text-sale font-semibold">Tersisa {{ $remaining }}</p>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
    (function () {
        const cd = document.getElementById('flash-countdown');
        if (cd) {
            let total = parseInt(cd.dataset.remaining || '0', 10);
            if (!total || total < 0) total = 0;
            const h = cd.querySelector('[data-cd="h"]');
            const m = cd.querySelector('[data-cd="m"]');
            const s = cd.querySelector('[data-cd="s"]');
            const pad = n => String(n).padStart(2, '0');
            function paint() {
                h.textContent = pad(Math.floor(total / 3600));
                m.textContent = pad(Math.floor((total % 3600) / 60));
                s.textContent = pad(total % 60);
            }
            paint();
            if (total > 0) {
                setInterval(function () {
                    total -= 1;
                    if (total < 0) { total = 0; }
                    paint();
                }, 1000);
            }
        }
    })();

    (function () {
        const section = document.getElementById('flash-sale');
        const track = document.getElementById('flash-track');
        if (!section || !track) return;

        const reals = track.querySelectorAll('[data-flash-card]');
        const N = reals.length;
        if (N <= 1) return;

        const DURATION_MS = 500;
        const AUTOPLAY_MS = 3000;
        const EASE = 'transform ' + DURATION_MS + 'ms cubic-bezier(.22,.61,.36,1)';
        let idx = 0;
        let animating = false;

        function step() {
            if (!reals[0]) return 0;
            const cardW = reals[0].getBoundingClientRect().width;
            const styles = getComputedStyle(track);
            const gap = parseFloat(styles.columnGap || styles.gap || '12') || 12;
            return cardW + gap;
        }

        function setX(px, withTransition) {
            track.style.transition = withTransition ? EASE : 'none';
            track.style.transform = 'translateX(-' + px + 'px)';
        }

        function go(target) {
            if (animating) return;
            const s = step();

            if (target >= N) {
                animating = true;
                setX(target * s, true);
                idx = target;
                window.setTimeout(() => {
                    idx = 0;
                    setX(0, false);
                    animating = false;
                }, DURATION_MS + 20);
                return;
            }

            if (target < 0) {
                animating = true;
                setX(N * s, false);
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        idx = N - 1;
                        setX(idx * s, true);
                        window.setTimeout(() => { animating = false; }, DURATION_MS + 20);
                    });
                });
                return;
            }

            idx = target;
            setX(idx * s, true);
        }

        const prevBtn = document.getElementById('flash-prev');
        const nextBtn = document.getElementById('flash-next');
        prevBtn?.addEventListener('click', () => { go(idx - 1); resetAutoplay(); });
        nextBtn?.addEventListener('click', () => { go(idx + 1); resetAutoplay(); });

        let timer = null;
        function startAutoplay() { stopAutoplay(); timer = window.setInterval(() => go(idx + 1), AUTOPLAY_MS); }
        function stopAutoplay() { if (timer) { window.clearInterval(timer); timer = null; } }
        function resetAutoplay() { stopAutoplay(); startAutoplay(); }
        startAutoplay();

        section.addEventListener('mouseenter', stopAutoplay);
        section.addEventListener('mouseleave', startAutoplay);
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) { stopAutoplay(); } else { startAutoplay(); }
        });

        // Touch swipe
        let touchStartX = 0;
        let touchActive = false;
        section.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            touchActive = true;
            stopAutoplay();
        }, { passive: true });
        section.addEventListener('touchend', (e) => {
            if (!touchActive) return;
            touchActive = false;
            const dx = e.changedTouches[0].clientX - touchStartX;
            if (Math.abs(dx) > 35) { go(dx > 0 ? idx - 1 : idx + 1); }
            startAutoplay();
        });

        // Recompute on resize
        let resizeTimer;
        window.addEventListener('resize', () => {
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(() => setX(idx * step(), false), 150);
        });
    })();
    </script>
    @endpush
    @endif
</div>
