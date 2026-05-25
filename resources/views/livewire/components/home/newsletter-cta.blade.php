{{--
    Premium Membership CTA. Mengarahkan ke halaman pembelian membership.
    Cashback % diambil dari global_config('cashback'); harga dari global_config('premium_membership_price').
--}}
<section aria-labelledby="premium-h" class="container-x mt-14 md:mt-10">
    <div class="grad-violet-emerald banknote-grain relative rounded-2xl overflow-hidden text-white shadow-card">
        <div class="relative grid md:grid-cols-2 gap-6 md:gap-10 items-center px-8 py-6 md:px-12 md:py-8">
            {{-- Dekoratif blob (di dalam div konten agar tidak kena .banknote-grain > * { position:relative } rule) --}}
            <div class="absolute -right-10 -top-10 w-72 h-72 rounded-full bg-tan5-300/20 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-10 -bottom-10 w-56 h-56 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>

            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur text-[11px] font-bold tracking-wider uppercase">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l2.39 4.84L19.78 8l-3.89 3.79.92 5.36L12 14.77 7.19 17.15l.92-5.36L4.22 8l5.39-1.16L12 2z"/></svg>
                    Paket Premium Membership
                </span>
                <h2 id="premium-h" class="font-display font-extrabold text-[28px] md:text-[36px] leading-tight tracking-tight mt-3">
                    Upgrade ke Premium,<br/>Dapatkan <span class="text-tan5-200">{{ global_config('cashback', 1) }}% Cashback</span> Instant
                </h2>
                <p class="text-white/80 mt-2 max-w-md text-[14px] md:text-[15px]">
                    Cashback otomatis masuk ke saldo member setiap pembelian, plus akses promo & penawaran eksklusif.
                </p>
            </div>

            <div class="flex flex-col gap-3">
                <ul class="space-y-2 text-[14px]">
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 mt-0.5 shrink-0 text-tan5-200" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9 16.2l-3.5-3.5L4 14.2l5 5 11-11-1.5-1.5z"/></svg>
                        <span><strong class="font-bold text-tan5-200">{{ global_config('cashback', 1) }}% Cashback</strong> instant untuk setiap pembelian</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 mt-0.5 shrink-0 text-tan5-200" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9 16.2l-3.5-3.5L4 14.2l5 5 11-11-1.5-1.5z"/></svg>
                        <span>Akses promo & voucher khusus member</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 mt-0.5 shrink-0 text-tan5-200" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9 16.2l-3.5-3.5L4 14.2l5 5 11-11-1.5-1.5z"/></svg>
                        <span>Prioritas notifikasi drop koleksi terbaru</span>
                    </li>
                </ul>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 bg-white rounded-xl p-3 shadow-card">
                    <div class="flex-1 px-2">
                        <p class="text-[11px] uppercase tracking-wider text-ink/60 font-bold">Mulai dari</p>
                        <p class="text-ink font-mono font-extrabold text-[20px] leading-none mt-1">{{ format_rupiah((int) global_config('premium_membership_price', 100000)) }}<span class="text-[12px] font-sans font-normal text-ink/60"> / tahun</span></p>
                    </div>
                    <a href="{{ route('premium.purchase') }}" class="h-12 inline-flex items-center justify-center px-6 rounded-lg bg-tan5-500 hover:bg-tan5-600 text-white font-bold tracking-wide text-[14px] uppercase transition-colors">
                        {{ auth()->check() ? 'Akses Premium' : 'Daftar Premium' }}
                    </a>
                </div>
                <p class="text-[12px] text-white/60">Dengan mendaftar, kamu setuju dengan <a class="underline" href="/terms-conditions">Syarat &amp; Ketentuan</a> kami.</p>
            </div>
        </div>
    </div>
</section>
