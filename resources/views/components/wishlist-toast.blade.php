{{-- Global wishlist toaster — listens for `wishlist-added` event from WishlistButton --}}
<div x-data="{
        show: false,
        message: '',
        active: true,
        manageUrl: '{{ route('user.wishlist') }}',
        timer: null,
        trigger(detail) {
            this.message = detail.message ?? 'Produk sudah dimasukkan ke daftar Wishlist Anda.';
            this.active = detail.active ?? true;
            if (detail.manageUrl) this.manageUrl = detail.manageUrl;
            this.show = true;
            if (this.timer) clearTimeout(this.timer);
            this.timer = setTimeout(() => { this.show = false; }, 4000);
        }
     }"
     x-on:wishlist-added.window="trigger($event.detail)"
     x-show="show"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-y-3"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-3"
     class="fixed bottom-[80px] md:bottom-6 left-4 right-4 md:left-auto md:right-6 z-[60] max-w-sm md:w-[340px] pointer-events-none"
     style="display: none;"
     role="status"
     aria-live="polite">
    <div class="pointer-events-auto bg-white rounded-xl shadow-card border border-black/10 p-3 flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-sale/10 text-sale shrink-0 grid place-items-center"
             :class="active ? 'bg-sale/10 text-sale' : 'bg-black/5 text-ink/55'">
            <template x-if="active">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 21s-7.5-4.5-9.5-9.5C1.2 8 3 5 6 5c2 0 3.5 1 4.5 2.5C11.5 6 13 5 15 5c3 0 4.8 3 3.5 6.5C19.5 16.5 12 21 12 21z"/>
                </svg>
            </template>
            <template x-if="!active">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </template>
        </div>
        <p class="flex-1 text-[13px] text-ink leading-snug" x-text="message"></p>
        <a :href="manageUrl"
           x-show="active"
           class="shrink-0 text-[12px] font-semibold text-emerald20-700 hover:text-emerald20-800 hover:underline">
            Kelola
        </a>
        <button type="button"
                x-on:click="show = false"
                class="shrink-0 w-6 h-6 grid place-items-center text-ink/40 hover:text-ink/80"
                aria-label="Tutup">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
