{{--
    Hero carousel section for home page.
    - Loops $banners (App\Models\Banner) when present, falls back to design's 3 hardcoded slides if empty.
    - Pure-JS autoplay/swipe (no Alpine.js dependency).
    TODO: when backend exposes per-banner CTA fields (banner->cta_label, banner->cta_url),
          render them inside each slide instead of generic "Belanja Sekarang".
--}}
<section aria-label="Promosi utama" class="container-x mt-4 md:mt-6">
    <div id="hero" class="relative overflow-hidden rounded-2xl shadow-card bg-paper">
        <div class="aspect-[4/3] md:aspect-[16/7] relative">
            @php $banners = $banners ?? collect(); @endphp
            @if($banners && $banners->count() > 0)
                @php $count = $banners->count(); @endphp
                <div id="hero-track" class="carousel-track flex h-full" style="width: {{ $count * 100 }}%;">
                    @foreach($banners as $idx => $banner)
                        <article class="relative h-full overflow-hidden bg-gray-100" style="width: {{ 100 / $count }}%;">
                            @if($banner->url)
                                <a href="{{ $banner->url }}" class="block w-full h-full">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($banner->image) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover select-none" draggable="false" loading="{{ $idx === 0 ? 'eager' : 'lazy' }}">
                                </a>
                            @else
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($banner->image) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover select-none" draggable="false" loading="{{ $idx === 0 ? 'eager' : 'lazy' }}">
                            @endif
                        </article>
                    @endforeach
                </div>
            @else
                {{-- Fallback: design's 3 banknote-themed slides --}}
                <div id="hero-track" class="carousel-track flex h-full" style="width: 300%;">
                    {{-- Slide 1: Hijau --}}
                    <article class="hero-green banknote-grain relative w-1/3 h-full text-white overflow-hidden">
                        <div class="absolute -right-16 -bottom-20 w-[420px] h-[420px] rounded-full border-[12px] border-white/10"></div>
                        <div class="absolute right-16 top-10 w-24 h-24 rounded-full border-[6px] border-white/15 hidden md:block"></div>
                        <div class="relative z-10 h-full flex items-center px-6 md:px-14">
                            <div class="max-w-[560px]">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur text-[12px] font-semibold tracking-wider uppercase">Koleksi Terbaru</span>
                                <h1 class="font-display font-extrabold text-[34px] md:text-[56px] leading-[1.05] mt-3 tracking-tight">Serba Murah,<br/>Serba Lengkap.</h1>
                                <p class="mt-3 md:mt-4 text-[14px] md:text-[16px] text-white/85 max-w-md">Fashion harian dari <strong class="font-semibold">Rp 25.000</strong>. Stok harian, kualitas dijaga, ongkir flat.</p>
                                <div class="mt-5 md:mt-7 flex flex-wrap gap-3">
                                    <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 h-11 md:h-12 px-5 md:px-6 rounded-lg bg-white text-emerald20-700 font-semibold text-[14px] md:text-[15px] hover:bg-paper transition-colors">
                                        Belanja Sekarang
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14"/><path d="m13 5 7 7-7 7"/></svg>
                                    </a>
                                    <a href="#flash-sale" class="inline-flex items-center gap-2 h-11 md:h-12 px-5 rounded-lg border border-white/40 text-white font-semibold text-[14px] hover:bg-white/10">⚡ Flash Sale</a>
                                </div>
                            </div>
                        </div>
                    </article>

                    {{-- Slide 2: Ungu --}}
                    <article class="hero-violet banknote-grain relative w-1/3 h-full text-white overflow-hidden">
                        <div class="absolute -left-20 -top-20 w-[360px] h-[360px] rounded-full bg-white/10 blur-2xl"></div>
                        <div class="relative z-10 h-full flex items-center px-6 md:px-14">
                            <div class="max-w-[560px]">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-tan5-300 text-tan5-800 text-[12px] font-bold tracking-wider uppercase">Hari Ini Saja</span>
                                <h1 class="font-display font-extrabold text-[34px] md:text-[56px] leading-[1.05] mt-3 tracking-tight">Flash Sale<br/>Mulai <span class="font-mono">Rp 25.000</span></h1>
                                <p class="mt-3 md:mt-4 text-[14px] md:text-[16px] text-white/85 max-w-md">Produk pilihan harga tetap. Stok terbatas — habis ya habis.</p>
                                <div class="mt-5">
                                    <a href="{{ route('catalog') }}?flash=1" class="inline-flex items-center gap-2 h-11 md:h-12 px-5 rounded-lg bg-tan5-300 hover:bg-tan5-200 text-tan5-800 font-bold transition-colors">Serbu Sekarang</a>
                                </div>
                            </div>
                        </div>
                    </article>

                    {{-- Slide 3: Tan --}}
                    <article class="hero-tan banknote-grain relative w-1/3 h-full text-tan5-800 overflow-hidden">
                        <div class="absolute right-0 top-0 bottom-0 w-1/2 hidden md:block">
                            <div class="absolute right-10 top-10 w-44 h-44 rounded-full bg-white/40 blur-xl"></div>
                            <div class="absolute right-32 bottom-12 w-28 h-28 rounded-full bg-violet10-200 blur-lg opacity-60"></div>
                        </div>
                        <div class="relative z-10 h-full flex items-center px-6 md:px-14">
                            <div class="max-w-[560px]">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-tan5-800 text-tan5-100 text-[12px] font-bold tracking-wider uppercase">Family Pack</span>
                                <h1 class="font-display font-extrabold text-[34px] md:text-[56px] leading-[1.05] mt-3 tracking-tight">Stelan Anak<br/>Mulai <span class="font-mono">Rp 89rb</span></h1>
                                <p class="mt-3 md:mt-4 text-[14px] md:text-[16px] text-tan5-700 max-w-md">Atasan + bawahan, motif lucu, bahan adem. Siap kirim hari ini juga.</p>
                                <div class="mt-5">
                                    <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 h-11 md:h-12 px-5 rounded-lg bg-tan5-800 hover:bg-tan5-700 text-white font-semibold text-[14px] transition-colors">Lihat Koleksi</a>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            @endif

            {{-- Prev / Next (desktop) --}}
            @php $totalSlides = ($banners && $banners->count() > 0) ? $banners->count() : 3; @endphp
            @if($totalSlides > 1)
                <button type="button" id="hero-prev" class="hidden md:grid place-items-center absolute left-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/90 hover:bg-white shadow-card text-ink z-10" aria-label="Slide sebelumnya">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m15 6-6 6 6 6"/></svg>
                </button>
                <button type="button" id="hero-next" class="hidden md:grid place-items-center absolute right-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/90 hover:bg-white shadow-card text-ink z-10" aria-label="Slide berikutnya">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m9 6 6 6-6 6"/></svg>
                </button>

                <div id="hero-dots" class="absolute bottom-3 md:bottom-5 left-1/2 -translate-x-1/2 flex items-center gap-2 z-10">
                    @for($i = 0; $i < $totalSlides; $i++)
                        <button type="button" data-dot="{{ $i }}" class="{{ $i === 0 ? 'w-8 h-2' : 'w-2 h-2' }} rounded-full {{ $i === 0 ? 'bg-white' : 'bg-white/50' }}" aria-label="Slide {{ $i + 1 }}"></button>
                    @endfor
                </div>
            @endif
        </div>
    </div>
</section>

@push('scripts')
<script>
(function () {
    const track = document.getElementById('hero-track');
    if (!track) return;
    const dots = document.querySelectorAll('#hero-dots [data-dot]');
    const prev = document.getElementById('hero-prev');
    const next = document.getElementById('hero-next');
    const N = dots.length || 0;
    if (N <= 1) return;
    let idx = 0;
    function go(i) {
        idx = ((i % N) + N) % N;
        track.style.transform = 'translateX(-' + (idx * (100 / N)) + '%)';
        dots.forEach((d, di) => {
            d.className = (di === idx ? 'w-8 h-2 rounded-full bg-white' : 'w-2 h-2 rounded-full bg-white/50');
        });
    }
    dots.forEach(d => d.addEventListener('click', () => { go(parseInt(d.dataset.dot)); resetTimer(); }));
    prev?.addEventListener('click', () => { go(idx - 1); resetTimer(); });
    next?.addEventListener('click', () => { go(idx + 1); resetTimer(); });

    let timer = null;
    function start() { timer = setInterval(() => go(idx + 1), 5500); }
    function stop() { if (timer) { clearInterval(timer); timer = null; } }
    function resetTimer() { stop(); start(); }
    start();
    const hero = document.getElementById('hero');
    hero.addEventListener('mouseenter', stop);
    hero.addEventListener('mouseleave', start);

    // Touch swipe
    let touchStartX = 0;
    hero.addEventListener('touchstart', (e) => { touchStartX = e.touches[0].clientX; }, { passive: true });
    hero.addEventListener('touchend', (e) => {
        const dx = e.changedTouches[0].clientX - touchStartX;
        if (Math.abs(dx) > 50) { go(dx > 0 ? idx - 1 : idx + 1); resetTimer(); }
    });
})();
</script>
@endpush
