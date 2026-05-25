{{-- TODO: pull shipping city from user profile/session; expose category list dynamically; popular keywords from search analytics --}}
@php
    $popularKeywords = ['Polo Misty Grey', 'Bomber Army', 'Stelan Anak', 'Kemeja Linen', 'Dress Wanita', 'Cargo Pants'];
    $isHome = request()->is('/');
    $isCatalog = request()->is('catalog*');
    $isBlog = request()->is('blog*');

    // Resolve cart count automatically if parent page didn't pass it.
    // Mirrors App\Livewire\Cart::getCartItemsProperty so the navbar badge stays in sync everywhere.
    if (! isset($cartCount)) {
        $cartSessionId = \Illuminate\Support\Facades\Session::get('cart_session_id');
        $cartCount = 0;
        if ($cartSessionId || auth()->check()) {
            $cartCount = \App\Models\CartItem::query()
                ->where(function ($q) use ($cartSessionId) {
                    if ($cartSessionId) {
                        $q->where('session_id', $cartSessionId);
                    }
                    if (auth()->check()) {
                        $q->orWhere('user_id', auth()->id());
                    }
                })
                ->count();
        }
    }
@endphp
<header id="site-header" class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-black/5">
    <div class="container-x">
        <div class="flex items-center gap-3 md:gap-5 h-16 md:h-[72px]">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0" aria-label="{{ global_config('site_name') }} beranda">
                <img src="{{ asset('storage/static/logo_new.webp') }}" alt="{{ global_config('site_name') ?? 'buatserba' }}" class="w-9 h-9 md:w-10 md:h-10 object-contain shrink-0" width="40" height="40" />
                <span class="font-display font-extrabold text-[20px] md:text-[22px] leading-none tracking-tight" style="color:#1B6B43">
                    buatserba<span class="align-super text-[10px] ml-0.5" style="color:#C4A57C">®</span>
                </span>
            </a>

            {{-- Search (desktop + tablet) --}}
            <form action="{{ route('catalog') }}" method="GET" class="flex-1 hidden sm:flex items-stretch h-11 md:h-12 rounded-xl bg-white border border-tan5-200 focus-within:border-emerald20-500 focus-within:ring-4 focus-within:ring-emerald20-100 transition-all overflow-hidden shadow-sm" role="search">
                {{-- Category dropdown (desktop only) --}}
                <div class="relative hidden md:flex items-center pl-3 pr-2 border-r border-tan5-200/70">
                    <select name="category" aria-label="Filter kategori" class="appearance-none bg-transparent text-[13px] font-medium text-emerald20-700 pr-6 outline-none cursor-pointer">
                        <option value="">Semua Kategori</option>
                        <option>Atasan</option>
                        <option>Bawahan</option>
                        <option>Outerwear</option>
                        <option>Anak</option>
                        <option>Aksesoris</option>
                        <option>Tas</option>
                    </select>
                    <svg class="absolute right-2 pointer-events-none text-emerald20-700" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                </div>
                <div class="flex-1 flex items-center min-w-0">
                    <span class="pl-4 pr-2 flex items-center text-tan5-600" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    </span>
                    <input type="search" name="search" value="{{ request('search') }}" aria-label="Cari produk" placeholder="Cari Polo, Bomber, Stelan Anak…" class="flex-1 min-w-0 bg-transparent pr-2 outline-none placeholder:text-black/35 text-[14px] md:text-[15px]" />
                </div>
                <button type="submit" class="flex items-center gap-1.5 px-4 md:px-5 bg-emerald20-600 hover:bg-emerald20-700 text-white text-[14px] font-semibold transition-colors">
                    <svg class="hidden md:block" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <span>Cari</span>
                </button>
            </form>

            {{-- Header icons (right side) --}}
            <div class="flex items-center gap-1 md:gap-2 ml-auto sm:ml-0">
                @auth
                    <button type="button" class="relative w-10 h-10 rounded-lg hover:bg-paper grid place-items-center" aria-label="Notifikasi">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 8a6 6 0 1 1 12 0c0 5 2 6 2 6H4s2-1 2-6Z"/><path d="M10 19a2 2 0 0 0 4 0"/></svg>
                        <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-sale"></span>
                    </button>
                @endauth
                <a href="{{ route('cart') }}" class="relative w-10 h-10 rounded-lg hover:bg-paper grid place-items-center" aria-label="Keranjang{{ $cartCount > 0 ? ' ('.$cartCount.' item)' : '' }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 4h2l2.6 12.2A2 2 0 0 0 9.6 18h8.8a2 2 0 0 0 2-1.6L22 8H6"/><circle cx="10" cy="21" r="1.5"/><circle cx="18" cy="21" r="1.5"/></svg>
                    @if($cartCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 min-w-[14px] h-[14px] px-[3px] rounded-full bg-sale text-white text-[9px] font-bold leading-none grid place-items-center ring-2 ring-white">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                    @endif
                </a>
                @auth
                    <div class="relative hidden md:block" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" @click="open = !open" class="flex items-center gap-2 px-3 h-10 rounded-lg hover:bg-paper" aria-label="Akun">
                            <span class="w-7 h-7 rounded-full bg-tan5-200 grid place-items-center text-tan5-700 font-bold text-[12px]">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                            <span class="text-sm font-medium">Akun</span>
                        </button>
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-cardHover border border-black/5 py-2 z-50" style="display:none;">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-ink hover:bg-paper">Dashboard</a>
                            <a href="{{ route('user.profile') }}" class="block px-4 py-2 text-sm text-ink hover:bg-paper">Profil</a>
                            <a href="{{ route('user.address') }}" class="block px-4 py-2 text-sm text-ink hover:bg-paper">Alamat</a>
                            <div class="border-t border-black/5 my-1"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-sale hover:bg-paper">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="hidden md:flex items-center gap-2">
                        <a href="{{ route('login') }}" class="px-3 h-10 rounded-lg text-sm font-medium text-ink hover:bg-paper inline-flex items-center">Masuk</a>
                        <a href="{{ route('register') }}" class="px-4 h-10 rounded-lg bg-emerald20-600 hover:bg-emerald20-700 text-white text-sm font-semibold inline-flex items-center">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>

        {{-- Sub-nav (desktop) --}}
        <nav class="hidden md:flex items-center gap-6 text-[14px] text-black/70 h-11 -mt-1" aria-label="Navigasi utama">
            <a href="{{ route('home') }}" class="{{ $isHome ? 'text-emerald20-700 font-semibold' : 'hover:text-ink' }} relative">
                Beranda
                @if($isHome)
                    <span class="absolute -bottom-2 left-0 right-0 h-0.5 bg-emerald20-600 rounded-full"></span>
                @endif
            </a>
            <a href="{{ route('catalog') }}" class="{{ $isCatalog ? 'text-emerald20-700 font-semibold' : 'hover:text-ink' }}">Katalog</a>
            <a href="{{ route('catalog') }}?promo=1" class="hover:text-ink">Promo</a>
            <a href="{{ route('catalog') }}?flash=1" class="hover:text-ink flex items-center gap-1">
                <span class="text-sale">⚡</span> Flash Sale
            </a>
            <a href="/faq" class="hover:text-ink">Bantuan</a>
        </nav>

        {{-- Popular chips (desktop) --}}
        <div class="hidden md:flex items-center gap-2 pb-2 -mt-1 overflow-x-auto no-scrollbar">
            <span class="text-[11px] font-semibold tracking-wider uppercase text-black/45 shrink-0">Populer</span>
            <span class="text-black/20">·</span>
            @foreach($popularKeywords as $kw)
                <a href="{{ route('catalog') }}?search={{ urlencode($kw) }}" class="shrink-0 text-[12px] px-2.5 py-1 rounded-full bg-paper border border-tan5-200/70 text-emerald20-700 hover:bg-emerald20-50 hover:border-emerald20-200 transition-colors">{{ $kw }}</a>
            @endforeach
        </div>

        {{-- Mobile search + popular chips --}}
        <div class="sm:hidden pb-3">
            <form action="{{ route('catalog') }}" method="GET" class="flex h-11 rounded-xl bg-white border border-tan5-200 focus-within:border-emerald20-500 focus-within:ring-4 focus-within:ring-emerald20-100 transition-all overflow-hidden shadow-sm" role="search">
                <span class="pl-3 pr-1 flex items-center text-tan5-600" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                </span>
                <input type="search" name="search" value="{{ request('search') }}" aria-label="Cari produk" placeholder="Cari produk…" class="flex-1 min-w-0 bg-transparent px-2 outline-none placeholder:text-black/35 text-[14px]" />
                <button type="submit" class="px-4 bg-emerald20-600 hover:bg-emerald20-700 text-white text-[13px] font-semibold">Cari</button>
            </form>
            <div class="flex items-center gap-2 mt-2 overflow-x-auto no-scrollbar">
                <span class="text-[11px] text-black/45 shrink-0">Populer:</span>
                @foreach(array_slice($popularKeywords, 0, 4) as $kw)
                    <a href="{{ route('catalog') }}?search={{ urlencode($kw) }}" class="shrink-0 text-[11px] px-2.5 py-1 rounded-full bg-paper border border-tan5-200/70 text-emerald20-700 hover:bg-emerald20-50">{{ $kw }}</a>
                @endforeach
            </div>
        </div>
    </div>
</header>
