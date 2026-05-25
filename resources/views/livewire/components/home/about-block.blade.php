@php
    $aboutSummary = $aboutSummary ?? '';
    // Stats: try global_config keys first, fallback to design defaults
    $stats = [
        ['accent' => 'tan5-300',     'icon_color' => 'text-tan5-200',     'value' => global_config('stat_products') ?: '10K+', 'label' => 'Produk fashion harian',   'svg' => '<path d="M3 7h18l-1.5 11.5A2 2 0 0 1 17.5 20H6.5a2 2 0 0 1-2-1.5L3 7Z"/><path d="M8 7V5a4 4 0 0 1 8 0v2"/>'],
        ['accent' => 'violet10-200', 'icon_color' => 'text-violet10-200', 'value' => global_config('stat_provinces') ?: '34',  'label' => 'Provinsi terjangkau',     'svg' => '<path d="M12 22s7-7 7-13a7 7 0 0 0-14 0c0 6 7 13 7 13Z"/><circle cx="12" cy="9" r="2.5"/>'],
        ['accent' => 'emerald20-200','icon_color' => 'text-emerald20-200','value' => global_config('stat_rating') ?: '4.8/5',  'label' => 'Rating pembeli',          'svg' => '<path d="m12 3 2.5 5.5 6 .5-4.5 4 1.5 6-5.5-3.5L6.5 19l1.5-6-4.5-4 6-.5L12 3Z"/>', 'fill' => true],
        ['accent' => 'tan5-300',     'icon_color' => 'text-tan5-200',     'value' => global_config('stat_customers') ?: '50K+','label' => 'Pelanggan puas',          'svg' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/>'],
    ];
@endphp
<section aria-labelledby="tentang-h" class="container-x mt-14 md:mt-24">

    {{-- A. Stat banner --}}
    <div class="grad-violet-emerald banknote-grain relative overflow-hidden rounded-2xl text-white shadow-card">
        <div class="absolute inset-0 pointer-events-none">
            <span class="absolute top-6 left-10 twinkle">✦</span>
            <span class="absolute bottom-8 right-16 twinkle" style="animation-delay:.5s">✦</span>
            <span class="absolute top-1/2 right-1/3 twinkle" style="animation-delay:1.1s">✦</span>
        </div>
        <div class="px-6 md:px-10 py-8 md:py-12">
            <div class="flex items-end justify-between flex-wrap gap-3 mb-6 md:mb-8">
                <div>
                    <p class="text-[12px] uppercase tracking-[0.25em] text-tan5-200 font-semibold">tentang {{ global_config('site_name') ?? 'buatserba' }}</p>
                    <h2 id="tentang-h" class="font-display font-extrabold text-[26px] md:text-[40px] leading-tight tracking-tight mt-1">Marketplace fashion <span class="text-tan5-200">harga jujur</span>.</h2>
                </div>
                <a href="/about" class="inline-flex items-center gap-2 h-11 px-5 rounded-lg bg-white text-emerald20-700 font-semibold text-sm hover:bg-paper">Pelajari Lebih Lanjut →</a>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                @foreach($stats as $stat)
                    <div class="relative">
                        <div class="absolute -left-1 top-0 bottom-0 w-1 rounded-full bg-{{ $stat['accent'] }}"></div>
                        <div class="pl-5">
                            <div class="{{ $stat['icon_color'] }}">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="{{ ($stat['fill'] ?? false) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">{!! $stat['svg'] !!}</svg>
                            </div>
                            <div class="font-mono font-extrabold text-[36px] md:text-[48px] leading-none mt-2">{{ $stat['value'] }}</div>
                            <p class="text-[13px] md:text-[14px] text-white/80 mt-2">{{ $stat['label'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- B. 3 Feature Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-5 mt-5 md:mt-6">
        <article class="relative bg-white rounded-2xl shadow-card hover:shadow-cardHover transition-all p-6 md:p-7 overflow-hidden border-t-4 border-tan5-300">
            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-tan5-300 to-transparent"></div>
            <div class="w-20 h-20 md:w-24 md:h-24 mb-3 text-tan5-700">
                <svg viewBox="0 0 96 96" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 30h52l-4 44a6 6 0 0 1-6 5.4H32a6 6 0 0 1-6-5.4L22 30Z"/>
                    <path d="M36 30v-6a12 12 0 0 1 24 0v6"/>
                    <path d="m38 56 8 8 16-18" stroke="#1B6B43" stroke-width="3"/>
                </svg>
            </div>
            <h3 class="font-display font-extrabold text-[20px] md:text-[22px] tracking-tight">100% Original</h3>
            <p class="text-[14px] text-black/60 mt-1">Verifikasi tiap kiriman, tidak ada KW</p>
        </article>

        <article class="relative bg-white rounded-2xl shadow-card hover:shadow-cardHover transition-all p-6 md:p-7 overflow-hidden border-t-4 border-violet10-300">
            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-violet10-300 to-transparent"></div>
            <div class="w-20 h-20 md:w-24 md:h-24 mb-3 text-violet10-500">
                <svg viewBox="0 0 96 96" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="20" y="22" width="56" height="56" rx="6"/>
                    <path d="M28 34h40M28 50h40M28 66h22"/>
                    <circle cx="62" cy="64" r="8" fill="#C4A57C" stroke="none"/>
                    <path d="M58 64h8M62 60v8" stroke="#fff" stroke-width="2.4"/>
                </svg>
            </div>
            <h3 class="font-display font-extrabold text-[20px] md:text-[22px] tracking-tight">Harga Pas Kantong</h3>
            <p class="text-[14px] text-black/60 mt-1">Tier tetap, tidak ada biaya tersembunyi</p>
        </article>

        <article class="relative bg-white rounded-2xl shadow-card hover:shadow-cardHover transition-all p-6 md:p-7 overflow-hidden border-t-4 border-emerald20-400">
            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-emerald20-400 to-transparent"></div>
            <div class="w-20 h-20 md:w-24 md:h-24 mb-3 text-emerald20-600">
                <svg viewBox="0 0 96 96" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 28h44v36H8z"/>
                    <path d="M52 38h22l10 10v16H52V38Z"/>
                    <circle cx="24" cy="70" r="6"/>
                    <circle cx="68" cy="70" r="6"/>
                    <path d="M2 36h6M2 46h12M2 56h8" stroke="#7758A8" stroke-width="2"/>
                </svg>
            </div>
            <h3 class="font-display font-extrabold text-[20px] md:text-[22px] tracking-tight">Sampai Cepat</h3>
            <p class="text-[14px] text-black/60 mt-1">Pengiriman 1–3 hari, tracking real-time</p>
        </article>
    </div>

</section>
