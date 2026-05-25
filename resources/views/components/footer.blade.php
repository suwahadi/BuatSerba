@php
    $whatsapp = global_config('whatsapp');
    if($whatsapp && \Illuminate\Support\Str::startsWith($whatsapp, '08')) {
        $whatsapp = '62' . substr($whatsapp, 1);
    }
@endphp
<footer class="mt-14 md:mt-24 bg-paper border-t border-black/5">
    <div class="container-x py-10 md:py-14">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-8">

            {{-- Brand + socials + about --}}
            <div class="col-span-2 md:col-span-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('storage/static/logo_new.webp') }}" alt="{{ global_config('site_name') ?? 'buatserba' }}" class="w-9 h-9 object-contain shrink-0" width="36" height="36" />
                    <span class="font-display font-extrabold text-[20px] text-emerald20-700">buatserba<span class="text-tan5-400 align-super text-[12px] ml-0.5">®</span></span>
                </a>
                <p class="text-[13px] text-black/65 mt-3 max-w-xs leading-relaxed">{{ \App\Models\Page::getAboutDescription() }}</p>
                <div class="mt-4 flex items-center gap-2">
                    <a href="#" class="social-pill" style="--c:#1B6B43" aria-label="Instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
                    </a>
                    <a href="#" class="social-pill" style="--c:#5C3F88" aria-label="TikTok">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5.8 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1.84-.1z"/></svg>
                    </a>
                    <a href="#" class="social-pill" style="--c:#A3835F" aria-label="Facebook">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M13 22v-8h3l1-4h-4V7.5c0-1.2.4-2 2.1-2H17V2.2C16.6 2.1 15.5 2 14.4 2 12 2 10 3.5 10 6.8V10H7v4h3v8h3Z"/></svg>
                    </a>
                    <a href="#" class="social-pill" style="--c:#1B6B43" aria-label="YouTube">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12s0-3.3-.4-4.9a2.6 2.6 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.3A2.6 2.6 0 0 0 2.4 7.1C2 8.7 2 12 2 12s0 3.3.4 4.9c.2 1 .9 1.6 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.3a2.6 2.6 0 0 0 1.8-1.8c.4-1.6.4-4.9.4-4.9Zm-12 3.5v-7l5.5 3.5L10 15.5Z"/></svg>
                    </a>
                </div>
            </div>

            {{-- About --}}
            <div>
                <h4 class="font-display font-bold text-[14px] mb-3 text-ink">Tentang Kami</h4>
                <ul class="space-y-2 text-[13px] text-black/65">
                    <li><a href="/about" class="hover:text-ink">Tentang</a></li>
                    <li><a href="/blog" class="hover:text-ink">Blog</a></li>
                    <li><a href="/faq" class="hover:text-ink">FAQ</a></li>
                </ul>
            </div>

            {{-- Categories --}}
            <div>
                <h4 class="font-display font-bold text-[14px] mb-3 text-ink">Kategori</h4>
                <ul class="space-y-2 text-[13px] text-black/65">
                    @if(isset($categories) && $categories && $categories->count())
                        @foreach($categories->take(5) as $category)
                            <li><a href="/{{ $category->slug }}" class="hover:text-ink">{{ $category->name }}</a></li>
                        @endforeach
                    @else
                        <li><a href="{{ route('catalog') }}" class="hover:text-ink">Semua Produk</a></li>
                    @endif
                </ul>
            </div>

            {{-- Help & Contact --}}
            <div>
                <h4 class="font-display font-bold text-[14px] mb-3 text-ink">Informasi</h4>
                <ul class="space-y-2 text-[13px] text-black/65">
                    <li><a href="/faq" class="hover:text-ink">Pusat Bantuan</a></li>
                    <li><a href="/return-refund-policy" class="hover:text-ink">Pengembalian</a></li>
                    <li><a href="/terms-conditions" class="hover:text-ink">Syarat &amp; Ketentuan</a></li>
                    <li><a href="/privacy-policy" class="hover:text-ink">Privasi</a></li>
                </ul>
                @if(global_config('email') || global_config('phone') || global_config('whatsapp'))
                    <div class="mt-4 space-y-1.5 text-[12px] text-black/60">
                        @if(global_config('email'))
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>{{ global_config('email') }}</span>
                            </div>
                        @endif
                        @if(global_config('whatsapp'))
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.978-1.42A9.956 9.956 0 0 0 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2Zm4.93 13.697c-.207.583-1.215 1.109-1.67 1.147-.455.038-.467.36-2.942-.715-2.475-1.074-3.96-3.706-4.079-3.874-.119-.168-.974-1.404-.924-2.647.05-1.242.72-1.835.96-2.083.24-.247.518-.304.69-.304.173 0 .345.002.496.01.16.008.373-.061.584.489.21.55.714 1.847.776 1.981.063.135.104.291.02.47-.083.177-.125.287-.248.44-.122.154-.257.345-.366.463-.123.13-.25.272-.107.533.143.26.636 1.145 1.366 1.855.938.903 1.726 1.181 1.973 1.314.247.133.39.112.534-.067.144-.179.618-.783.783-1.052.165-.268.33-.224.557-.134.227.09 1.44.744 1.687.88.248.133.413.2.474.31.062.11.062.636-.145 1.22Z"/></svg>
                                <a href="https://wa.me/{{ $whatsapp }}" class="hover:text-ink">{{ global_config('whatsapp') }}</a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>

    <div class="border-t border-black/5">
        <div class="container-x py-5 flex flex-col md:flex-row items-center gap-3 justify-between">
            <p class="text-[12px] text-black/55">&copy; {{ date('Y') }} {{ global_config('company_name') ?? 'buatserba.com' }}. All Rights Reserved.</p>
            <div class="flex items-center gap-3 text-[12px] text-black/55">
                <a href="/privacy-policy" class="hover:text-ink">Privasi</a>
                <span class="opacity-50">·</span>
                <a href="/terms-conditions" class="hover:text-ink">Syarat &amp; Ketentuan</a>
                <span class="opacity-50">·</span>
                <a href="/return-refund-policy" class="hover:text-ink">Kebijakan Retur</a>
            </div>
        </div>
    </div>
</footer>
