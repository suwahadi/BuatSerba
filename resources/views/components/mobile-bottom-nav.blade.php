@php
    $isHome = request()->is('/');
    $isCatalog = request()->is('catalog*');
    $isCart = request()->is('cart*');
    $isWishlist = request()->is('user/wishlist*');
    $isAccount = (request()->is('user*') && ! $isWishlist) || request()->is('settings*');
@endphp
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur border-t border-black/10 bnav-safe" aria-label="Navigasi mobile">
    <ul class="grid grid-cols-5 h-[60px]">
        <li>
            <a href="{{ route('home') }}" class="bnav {{ $isHome ? 'active' : '' }}" @if($isHome) aria-current="page" @endif>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
                <span>Beranda</span>
            </a>
        </li>
        <li>
            <a href="{{ route('catalog') }}" class="bnav {{ $isCatalog ? 'active' : '' }}" @if($isCatalog) aria-current="page" @endif>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                <span>Katalog</span>
            </a>
        </li>
        <li>
            <a href="{{ route('cart') }}" class="bnav {{ $isCart ? 'active' : '' }}" @if($isCart) aria-current="page" @endif>
                <span class="relative">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 4h2l2.6 12.2A2 2 0 0 0 9.6 18h8.8a2 2 0 0 0 2-1.6L22 8H6"/><circle cx="10" cy="21" r="1.5"/><circle cx="18" cy="21" r="1.5"/></svg>
                    @if(isset($cartCount) && $cartCount > 0)
                        <span class="absolute -top-1.5 -right-2 min-w-[16px] h-4 px-1 rounded-full bg-sale text-white text-[10px] font-bold grid place-items-center">{{ $cartCount }}</span>
                    @else
                        <span class="cart-count-bnav absolute -top-1.5 -right-2 min-w-[16px] h-4 px-1 rounded-full bg-sale text-white text-[10px] font-bold grid place-items-center" style="display:none">0</span>
                    @endif
                </span>
                <span>Keranjang</span>
            </a>
        </li>
        <li>
            <a href="{{ route('user.wishlist') }}" class="bnav {{ $isWishlist ? 'active' : '' }}" @if($isWishlist) aria-current="page" @endif>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="{{ $isWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 1 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z"/></svg>
                <span>Wishlist</span>
            </a>
        </li>
        <li>
            @auth
                <a href="{{ route('dashboard') }}" class="bnav {{ $isAccount ? 'active' : '' }}" @if($isAccount) aria-current="page" @endif>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                    <span>Akun</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="bnav">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                    <span>Masuk</span>
                </a>
            @endauth
        </li>
    </ul>
</nav>
