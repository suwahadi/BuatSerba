@php
    $categories = $categories ?? collect();

    // Palet warna persis dengan tier-slider (banknote gradients).
    $stylePalette = [
        ['class' => 'grad-tier-15',  'text' => 'text-white',    'badgeBg' => 'bg-white/85', 'badgeText' => 'text-[#4A4A45]',     'captionText' => 'text-white/85', 'tagText' => 'text-white/90'],
        ['class' => 'grad-tier-25',  'text' => 'text-tan5-900', 'badgeBg' => 'bg-white/70', 'badgeText' => 'text-tan5-700',      'captionText' => 'text-tan5-800', 'tagText' => 'text-tan5-700'],
        ['class' => 'grad-tier-35',  'text' => 'text-white',    'badgeBg' => 'bg-white/85', 'badgeText' => 'text-violet10-600',  'captionText' => 'text-white/85', 'tagText' => 'text-white/90'],
        ['class' => 'grad-tier-45',  'text' => 'text-white',    'badgeBg' => 'bg-white/85', 'badgeText' => 'text-emerald20-700', 'captionText' => 'text-white/85', 'tagText' => 'text-white/90'],
        ['class' => 'grad-tier-95',  'text' => 'text-white',    'badgeBg' => 'bg-white/85', 'badgeText' => 'text-[#1F4F87]',     'captionText' => 'text-white/85', 'tagText' => 'text-white/90'],
        ['class' => 'grad-tier-mix', 'text' => 'text-ink',      'badgeBg' => 'bg-white/85', 'badgeText' => 'text-ink',           'captionText' => 'text-ink/70',   'tagText' => 'text-ink/70'],
    ];
@endphp
<section id="quick-cat" aria-labelledby="quick-cat-heading" class="container-x mt-10 md:mt-16">
    <div class="flex items-end justify-between mb-3 md:mb-4">
        <div>
            <p class="text-[12px] uppercase tracking-[0.2em] text-tan5-600 font-semibold">Jelajahi Kategori</p>
            <h2 id="quick-cat-heading" class="font-display font-extrabold text-[22px] md:text-[30px] tracking-tight mt-1">Kategori Pakaian</h2>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="quick-cat-prev" class="w-9 h-9 grid place-items-center rounded-full border border-black/10 bg-white hover:bg-paper" aria-label="Kategori sebelumnya"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m15 6-6 6 6 6"/></svg></button>
            <button type="button" id="quick-cat-next" class="w-9 h-9 grid place-items-center rounded-full border border-black/10 bg-white hover:bg-paper" aria-label="Kategori berikutnya"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m9 6 6 6-6 6"/></svg></button>
        </div>
    </div>

    <div id="quick-cat-slider" class="flex gap-3 md:gap-4 overflow-x-auto no-scrollbar snap-x snap-mandatory scroll-smooth pb-1">
        @foreach($categories as $i => $category)
            @php
                $s = $stylePalette[$i % count($stylePalette)];
                // Auto-shrink ketika nama kategori panjang (mirip behaviour "Serbi" di tier-slider).
                $valueSize = mb_strlen($category->name) > 4
                    ? 'text-[24px] md:text-[30px]'
                    : 'text-[36px] md:text-[44px]';
            @endphp
            <a href="/{{ $category->slug }}" class="quick-cat-slide snap-start shrink-0 w-[160px] md:w-[200px] relative overflow-hidden rounded-2xl shadow-card hover:shadow-cardHover transition-shadow {{ $s['class'] }} banknote-grain aspect-[5/6] flex flex-col p-4">
                <div class="flex items-center justify-between gap-2">
                    <span class="px-2 py-0.5 rounded-full {{ $s['badgeBg'] }} {{ $s['badgeText'] }} text-[10px] font-bold tracking-wider uppercase truncate max-w-[110px]">{{ $category->name }}</span>
                    <span class="font-mono font-bold text-[10px] {{ $s['tagText'] }} shrink-0">/{{ $category->slug }}</span>
                </div>
                <div class="mt-auto {{ $s['text'] }}">
                    <div class="flex items-baseline gap-1 flex-wrap">
                        <span class="font-display {{ $valueSize }} font-extrabold leading-none tracking-tight">{{ $category->name }}</span>
                    </div>
                    <p class="text-[11px] {{ $s['captionText'] }} mt-1">{{ $category->description }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>

@push('scripts')
<script>
(function () {
    const slider = document.getElementById('quick-cat-slider');
    const prev   = document.getElementById('quick-cat-prev');
    const next   = document.getElementById('quick-cat-next');
    if (!slider || !prev || !next) return;

    const originals = Array.from(slider.children);
    const N = originals.length;
    if (N === 0) return;

    function makeClone(node) {
        const c = node.cloneNode(true);
        c.setAttribute('aria-hidden', 'true');
        c.dataset.clone = '1';
        if (c.tagName === 'A') c.setAttribute('tabindex', '-1');
        c.querySelectorAll('a, button').forEach(el => el.setAttribute('tabindex', '-1'));
        return c;
    }

    const firstOriginal = originals[0];
    originals.forEach(node => slider.insertBefore(makeClone(node), firstOriginal));
    originals.forEach(node => slider.appendChild(makeClone(node)));

    function step() {
        const card = slider.querySelector('.quick-cat-slide');
        return card ? card.getBoundingClientRect().width + 16 : 200;
    }
    const groupWidth = () => step() * N;

    let isWrapping = false;
    function jumpTo(left) {
        const prevSnap = slider.style.scrollSnapType;
        const prevBeh  = slider.style.scrollBehavior;
        slider.style.scrollSnapType  = 'none';
        slider.style.scrollBehavior  = 'auto';
        slider.scrollLeft = left;
        requestAnimationFrame(() => {
            slider.style.scrollSnapType = prevSnap;
            slider.style.scrollBehavior = prevBeh;
        });
    }

    requestAnimationFrame(() => jumpTo(groupWidth()));

    function checkWrap() {
        if (isWrapping) return;
        const sz = groupWidth();
        const sl = slider.scrollLeft;
        if (sl >= sz * 2) {
            isWrapping = true;
            jumpTo(sl - sz);
            setTimeout(() => { isWrapping = false; }, 60);
        } else if (sl < sz) {
            if (sl < sz - 1) {
                isWrapping = true;
                jumpTo(sl + sz);
                setTimeout(() => { isWrapping = false; }, 60);
            }
        }
    }

    let wrapTimer;
    slider.addEventListener('scroll', () => {
        if (isWrapping) return;
        clearTimeout(wrapTimer);
        wrapTimer = setTimeout(checkWrap, 140);
    }, { passive: true });

    prev.addEventListener('click', () => slider.scrollBy({ left: -step(), behavior: 'smooth' }));
    next.addEventListener('click', () => slider.scrollBy({ left:  step(), behavior: 'smooth' }));

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => jumpTo(groupWidth()), 180);
    });
})();
</script>
@endpush
