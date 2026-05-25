@php
    $isCard = $variant === 'card';
    $isDetail = $variant === 'detail';

    $wrapperClass = match($variant) {
        'detail' => 'px-3.5 py-2.5 sm:py-3 border rounded-xl shadow-card inline-flex items-center justify-center order-2 sm:order-3 transition-colors '
            . ($isActive
                ? 'border-sale/40 bg-sale/10 text-sale'
                : 'border-black/10 text-ink/55 hover:text-sale hover:border-sale/40 hover:bg-sale/5'),
        default => 'absolute top-2 right-2 z-10 w-8 h-8 rounded-full backdrop-blur bg-white/85 shadow-card inline-flex items-center justify-center transition-colors '
            . ($isActive
                ? 'text-sale'
                : 'text-ink/45 hover:text-sale'),
    };
@endphp

<button type="button"
        wire:click.prevent.stop="toggle"
        wire:loading.attr="disabled"
        class="{{ $wrapperClass }}"
        aria-pressed="{{ $isActive ? 'true' : 'false' }}"
        aria-label="{{ $isActive ? 'Hapus dari wishlist' : 'Tambah ke wishlist' }}">
    @if($isActive)
        <svg class="{{ $isDetail ? 'w-5 h-5' : 'w-4 h-4' }}" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 21s-7.5-4.5-9.5-9.5C1.2 8 3 5 6 5c2 0 3.5 1 4.5 2.5C11.5 6 13 5 15 5c3 0 4.8 3 3.5 6.5C19.5 16.5 12 21 12 21z"/>
        </svg>
    @else
        <svg class="{{ $isDetail ? 'w-5 h-5' : 'w-4 h-4' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
    @endif
</button>
