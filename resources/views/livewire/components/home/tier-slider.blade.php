{{--
    Price-tier slider — "Belanja Sesuai Kantong"
    TODO: link each tier to a price-filter URL on /catalog when filter param is supported,
          or expose a Tier model with name/min/max/sort.
--}}
@php
    $tiers = [
        ['label' => 'Tier 0', 'tag' => '2K IDR', 'word' => 'Serba', 'value' => '15', 'caption' => 'Mulai paling murah', 'class' => 'grad-tier-15', 'text' => 'text-white', 'badgeBg' => 'bg-white/85', 'badgeText' => 'text-[#4A4A45]', 'priceMax' => 15000],
        ['label' => 'Tier 1', 'tag' => '5K IDR',  'word' => 'Serba', 'value' => '25', 'caption' => 'Ringan dompet',     'class' => 'grad-tier-25', 'text' => 'text-tan5-900', 'badgeBg' => 'bg-white/70', 'badgeText' => 'text-tan5-700', 'priceMax' => 25000],
        ['label' => 'Tier 2', 'tag' => '10K IDR', 'word' => 'Serba', 'value' => '35', 'caption' => 'Sweet spot',         'class' => 'grad-tier-35', 'text' => 'text-white', 'badgeBg' => 'bg-white/85', 'badgeText' => 'text-violet10-600', 'priceMax' => 35000],
        ['label' => 'Tier 3', 'tag' => '20K IDR', 'word' => 'Serba', 'value' => '45', 'caption' => 'Premium ramah',      'class' => 'grad-tier-45', 'text' => 'text-white', 'badgeBg' => 'bg-white/85', 'badgeText' => 'text-emerald20-700', 'priceMax' => 45000],
        ['label' => 'Tier 4', 'tag' => '50K IDR', 'word' => 'Serba', 'value' => '95', 'caption' => 'Edisi premium',      'class' => 'grad-tier-95', 'text' => 'text-white', 'badgeBg' => 'bg-white/85', 'badgeText' => 'text-[#1F4F87]', 'priceMax' => 95000],
        ['label' => 'Promo',  'tag' => '15rb–89rb','word' => 'Serba-','value' => 'Serbi','caption' => 'Bundling spesial','class' => 'grad-tier-mix','text' => 'text-ink', 'badgeBg' => 'bg-white/85', 'badgeText' => 'text-ink', 'priceMax' => null],
    ];
@endphp
<section id="tiers" aria-labelledby="tier-heading" class="container-x mt-6 md:mt-10">
    <div class="flex items-end justify-between mb-3 md:mb-4">
        <div>
            <p class="text-[12px] uppercase tracking-[0.2em] text-emerald20-600 font-semibold">Pilih Berdasarkan Harga</p>
            <h2 id="tier-heading" class="font-display font-extrabold text-[22px] md:text-[30px] tracking-tight mt-1">Belanja Sesuai Kantong</h2>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="tier-prev" class="w-9 h-9 grid place-items-center rounded-full border border-black/10 bg-white hover:bg-paper" aria-label="Tier sebelumnya"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m15 6-6 6 6 6"/></svg></button>
            <button type="button" id="tier-next" class="w-9 h-9 grid place-items-center rounded-full border border-black/10 bg-white hover:bg-paper" aria-label="Tier berikutnya"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m9 6 6 6-6 6"/></svg></button>
        </div>
    </div>

    <div id="tier-slider" class="flex gap-3 md:gap-4 overflow-x-auto no-scrollbar snap-x snap-mandatory scroll-smooth pb-1">
        @foreach($tiers as $tier)
            @php
                $href = $tier['priceMax']
                    ? route('catalog') . '?price_max=' . $tier['priceMax']
                    : route('catalog') . '?promo=1';
            @endphp
            <a href="{{ $href }}" class="tier-slide snap-start shrink-0 w-[160px] md:w-[200px] relative overflow-hidden rounded-2xl shadow-card hover:shadow-cardHover transition-shadow {{ $tier['class'] }} banknote-grain aspect-[5/6] flex flex-col p-4">
                <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded-full {{ $tier['badgeBg'] }} {{ $tier['badgeText'] }} text-[10px] font-bold tracking-wider uppercase">{{ $tier['label'] }}</span>
                    <span class="font-mono font-bold text-[10px] {{ $tier['text'] === 'text-tan5-900' ? 'text-tan5-700' : 'text-white/90' }}">{{ $tier['tag'] }}</span>
                </div>
                <div class="mt-auto {{ $tier['text'] }}">
                    <div class="flex items-baseline gap-1">
                        <span class="font-display text-[12px] font-bold">{{ $tier['word'] }}</span>
                        <span class="font-display text-[36px] md:text-[44px] font-extrabold leading-none tracking-tight {{ $tier['value'] === 'Serbi' ? 'text-[28px] md:text-[34px]' : '' }}">{{ $tier['value'] }}</span>
                    </div>
                    <p class="text-[11px] {{ $tier['text'] === 'text-tan5-900' ? 'text-tan5-800' : ($tier['text'] === 'text-ink' ? 'text-ink/70' : 'text-white/85') }} mt-1">{{ $tier['caption'] }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>

@push('scripts')
<script>
(function () {
    const slider = document.getElementById('tier-slider');
    const prev = document.getElementById('tier-prev');
    const next = document.getElementById('tier-next');
    if (!slider || !prev || !next) return;
    function step() {
        const card = slider.querySelector('.tier-slide');
        return card ? card.getBoundingClientRect().width + 16 : 200;
    }
    prev.addEventListener('click', () => slider.scrollBy({ left: -step(), behavior: 'smooth' }));
    next.addEventListener('click', () => slider.scrollBy({ left:  step(), behavior: 'smooth' }));
})();
</script>
@endpush
