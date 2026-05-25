<div class="bg-white">
    <x-navbar :cartCount="$cartItems->count()" />

    {{-- Breadcrumb — seragam dengan catalog & product-detail --}}
    <nav class="border-b border-black/5" aria-label="Breadcrumb">
        <div class="container-x py-2.5">
            <ol class="flex items-center gap-1.5 text-[12px]">
                <li><a href="/" class="text-black/55 hover:text-emerald20-600 transition-colors">Beranda</a></li>
                <li class="text-black/30">/</li>
                <li class="font-semibold text-ink">Keranjang</li>
            </ol>
        </div>
    </nav>

    {{-- Page header --}}
    <header class="container-x pt-5 sm:pt-7 pb-4">
        <p class="text-[11px] uppercase tracking-[0.2em] text-tan5-600 font-semibold">Checkout · Langkah 1 dari 4</p>
        <div class="flex flex-wrap items-end justify-between gap-3 mt-1">
            <div>
                <h1 class="font-display font-extrabold text-[22px] md:text-[28px] tracking-tight text-ink">Keranjang Belanja</h1>
            </div>
        </div>
    </header>

    <div class="container-x pb-6 md:pb-10 lg:pb-14">
        {{-- Stepper --}}
        <div class="mb-6 sm:mb-8 overflow-x-auto no-scrollbar">
            <ol class="flex items-center justify-center gap-2 sm:gap-3 md:gap-4 min-w-max">
                @php
                    $steps = [
                        ['n' => 1, 'label' => 'Keranjang',  'state' => 'active'],
                        ['n' => 2, 'label' => 'Checkout',   'state' => 'pending'],
                        ['n' => 3, 'label' => 'Pembayaran', 'state' => 'pending'],
                        ['n' => 4, 'label' => 'Selesai',    'state' => 'pending'],
                    ];
                @endphp
                @foreach($steps as $i => $step)
                    <li class="flex items-center gap-2 sm:gap-3 shrink-0">
                        <span class="grid place-items-center w-7 h-7 sm:w-8 sm:h-8 rounded-full text-[12px] sm:text-[13px] font-mono font-bold
                            {{ $step['state'] === 'active'
                                ? 'bg-emerald20-600 text-white shadow-card'
                                : 'bg-paper border border-black/10 text-black/45' }}">
                            {{ $step['n'] }}
                        </span>
                        <span class="text-[11px] sm:text-[12px] font-semibold uppercase tracking-wider hidden sm:inline
                            {{ $step['state'] === 'active' ? 'text-ink' : 'text-black/45' }}">
                            {{ $step['label'] }}
                        </span>
                    </li>
                    @if($i < count($steps) - 1)
                        <li class="w-6 sm:w-10 md:w-14 h-px bg-black/10 shrink-0" aria-hidden="true"></li>
                    @endif
                @endforeach
            </ol>
        </div>

        <div class="grid grid-cols-1 {{ $cartItems->count() > 0 ? 'lg:grid-cols-3' : '' }} gap-6 lg:gap-8">

            {{-- ===== Items list ===== --}}
            <div class="{{ $cartItems->count() > 0 ? 'lg:col-span-2' : 'w-full' }}">
                <div class="bg-white rounded-2xl shadow-card border border-black/[0.04] p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4 sm:mb-5">
                        <h2 class="font-display font-bold text-[16px] sm:text-[18px] text-ink">Item Keranjang</h2>
                        @if($cartItems->count() > 0)
                            <span class="font-mono text-[12px] text-black/55">{{ $cartItems->count() }} produk</span>
                        @endif
                    </div>

                    @if($cartItems->isEmpty())
                        {{-- Empty state --}}
                        <div class="bg-paper/60 border border-black/5 rounded-2xl text-center py-12 px-6">
                            <div class="w-14 h-14 mx-auto rounded-full bg-white border border-black/[0.04] grid place-items-center shadow-card">
                                <svg class="w-7 h-7 text-tan5-500" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 6h13"/>
                                    <circle cx="9" cy="20" r="1.5"/>
                                    <circle cx="17" cy="20" r="1.5"/>
                                </svg>
                            </div>
                            <h3 class="mt-3 font-display font-bold text-[16px] text-ink">Keranjang belanja Anda kosong</h3>
                            <p class="mt-1 text-[13px] text-black/55 max-w-sm mx-auto">Mulai berbelanja dan tambahkan produk ke keranjang Anda.</p>
                            <a href="/catalog" class="mt-5 inline-flex items-center gap-1.5 px-5 py-2.5 grad-violet-emerald hover:opacity-95 text-white text-[13px] font-semibold rounded-xl shadow-card transition-opacity">
                                Mulai Belanja
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" d="M5 12h14"/><path stroke-linecap="round" d="m13 5 7 7-7 7"/></svg>
                            </a>
                        </div>
                    @else
                        {{-- Items --}}
                        <ul class="divide-y divide-black/5">
                            @foreach($cartItems as $item)
                                @php $attrs = $item->sku->attributes ?? []; @endphp
                                <li class="py-4 first:pt-0 last:pb-0" wire:key="cart-item-{{ $item->id }}">
                                    <div class="flex gap-3 sm:gap-4">
                                        {{-- Image --}}
                                        <a href="/product/{{ $item->product->slug }}?sku={{ $item->sku->id }}" class="shrink-0">
                                            <img src="{{ image_url($item->sku->image ?? $item->product->main_image) }}"
                                                 alt="{{ $item->product->name }}"
                                                 class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-xl bg-paper border border-black/[0.04]"
                                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27100%27 height=%27100%27%3E%3Crect width=%27100%27 height=%27100%27 fill=%27%23f5f5f0%27/%3E%3C/svg%3E'">
                                        </a>

                                        {{-- Body --}}
                                        <div class="flex-1 min-w-0 flex flex-col sm:flex-row sm:items-start gap-3">
                                            {{-- Info --}}
                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-[13px] sm:text-[14px] font-semibold leading-snug text-ink line-clamp-2">
                                                    <a href="/product/{{ $item->product->slug }}?sku={{ $item->sku->id }}" class="hover:text-emerald20-700 transition-colors">
                                                        {{ $item->product->name }}
                                                    </a>
                                                </h3>

                                                @if(! empty($attrs))
                                                    <div class="mt-1.5 flex flex-wrap gap-1">
                                                        @if(isset($attrs['name']) && trim((string) $attrs['name']) !== '')
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-paper text-ink/75 border border-black/5">{{ $attrs['name'] }}</span>
                                                        @else
                                                            @foreach($attrs as $k => $v)
                                                                @if($k === 'image' || trim((string) $v) === '') @continue @endif
                                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-paper text-ink/75 border border-black/5">{{ $v }}</span>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                @endif

                                                <p class="mt-2 font-mono font-extrabold text-emerald20-700 text-[14px] sm:text-[15px]">{{ format_rupiah($item->price) }}</p>

                                                @if($item->sku->stock_quantity < 5)
                                                    <p class="mt-1 text-[11px] text-sale font-semibold">⚠ Stok tersisa {{ $item->sku->stock_quantity }}</p>
                                                @endif
                                            </div>

                                            {{-- Quantity + actions --}}
                                            <div class="flex items-center justify-between sm:flex-col sm:items-end sm:justify-start gap-2 sm:gap-3 sm:min-w-[120px]">
                                                <div class="inline-flex items-center bg-white border border-black/10 rounded-xl overflow-hidden">
                                                    <button wire:click="decrementQuantity({{ $item->id }})" wire:loading.attr="disabled"
                                                            class="px-2.5 py-1.5 hover:bg-paper active:bg-paper/80 transition-colors text-ink/70 border-r border-black/5 disabled:opacity-50"
                                                            aria-label="Kurangi">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M20 12H4"/></svg>
                                                    </button>
                                                    <input type="number"
                                                           wire:change="updateQuantity({{ $item->id }}, $event.target.value)"
                                                           value="{{ $item->quantity }}"
                                                           min="1" max="{{ $item->sku->stock_quantity }}"
                                                           class="w-10 sm:w-12 text-center py-1.5 text-[13px] font-mono font-bold text-ink bg-transparent focus:outline-none border-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                                    <button wire:click="incrementQuantity({{ $item->id }})" wire:loading.attr="disabled"
                                                            class="px-2.5 py-1.5 hover:bg-paper active:bg-paper/80 transition-colors text-ink/70 border-l border-black/5 disabled:opacity-50"
                                                            aria-label="Tambah">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                                                    </button>
                                                </div>

                                                <div class="flex flex-col items-end gap-1">
                                                    <p class="font-mono font-extrabold text-[14px] sm:text-[15px] text-ink whitespace-nowrap">{{ format_rupiah($item->price * $item->quantity) }}</p>
                                                    <button wire:click="removeItem({{ $item->id }})"
                                                            wire:confirm="Apakah Anda yakin ingin menghapus item ini?"
                                                            class="inline-flex items-center gap-1 text-[11px] text-sale hover:text-sale/80 font-semibold transition-colors">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-5 pt-5 border-t border-black/5 flex items-center justify-between">
                            <a href="/catalog" class="inline-flex items-center gap-1.5 text-[13px] text-emerald20-700 hover:text-emerald20-800 font-semibold transition-colors">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" d="M19 12H5"/><path stroke-linecap="round" d="m11 19-7-7 7-7"/></svg>
                                Lanjut Belanja
                            </a>
                            <button wire:click="clearCart"
                                    wire:confirm="Apakah Anda yakin ingin mengosongkan keranjang?"
                                    class="text-[12px] text-sale hover:text-sale/80 font-semibold transition-colors">
                                Hapus Semua
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ===== Order Summary ===== --}}
            @if($cartItems->count() > 0)
                <aside class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-card border border-black/[0.04] p-5 sm:p-6 sticky top-24">
                        <h3 class="font-display font-bold text-[16px] text-ink mb-4">Ringkasan Pesanan</h3>

                        <dl class="space-y-2 mb-4 text-[13px]">
                            <div class="flex items-center justify-between">
                                <dt class="text-black/60">Subtotal <span class="text-black/40">({{ $cartItems->count() }} item)</span></dt>
                                <dd class="font-mono font-semibold text-ink">{{ format_rupiah($subtotal) }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-black/60">Ongkos Kirim</dt>
                                <dd class="text-[12px] italic text-black/40">Dihitung saat checkout</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-black/60">Biaya Layanan</dt>
                                <dd class="font-mono font-semibold text-ink">{{ format_rupiah($serviceFee) }}</dd>
                            </div>

                            @if($discount > 0)
                                <div class="pt-3 mt-3 border-t border-dashed border-emerald20-200">
                                    <div class="flex items-center justify-between bg-emerald20-50 border border-emerald20-100 rounded-lg p-2.5 mb-2">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <svg class="w-4 h-4 text-emerald20-700 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                            <span class="text-[11px] font-semibold text-emerald20-800 truncate">Voucher · <span class="font-mono">{{ strtoupper($promoCode) }}</span></span>
                                        </div>
                                        <button wire:click="removePromoCode" class="text-[11px] text-sale hover:text-sale/80 font-semibold ml-2 shrink-0">Hapus</button>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt class="text-emerald20-700 font-semibold">Potongan Harga</dt>
                                        <dd class="font-mono font-bold text-emerald20-700">−{{ format_rupiah($discount) }}</dd>
                                    </div>
                                </div>
                            @endif
                        </dl>

                        {{-- Total --}}
                        <div class="pt-4 mt-4 border-t border-black/5">
                            <div class="flex items-baseline justify-between">
                                <span class="font-display font-bold text-[14px] text-ink">Total</span>
                                <span class="font-mono font-extrabold text-[20px] sm:text-[22px] text-emerald20-700">{{ format_rupiah($total) }}</span>
                            </div>
                        </div>

                        {{-- Promo --}}
                        <div class="mt-5">
                            <label class="text-[10px] text-black/45 uppercase tracking-wider font-semibold">Kode Voucher</label>
                            <div class="flex gap-2 mt-1">
                                <input type="text" wire:model="promoCode" placeholder="Masukkan kode"
                                       class="flex-1 px-3 py-2 bg-paper border border-black/10 rounded-lg text-[13px] font-mono text-ink focus:outline-none focus:border-emerald20-500 focus:bg-white">
                                <button wire:click="applyPromoCode" wire:loading.attr="disabled"
                                        class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 border border-black/10 rounded-lg text-[12px] font-semibold text-ink transition-colors disabled:opacity-50 shrink-0">
                                    <span wire:loading.remove wire:target="applyPromoCode">Terapkan</span>
                                    <span wire:loading wire:target="applyPromoCode">...</span>
                                </button>
                            </div>
                        </div>

                        {{-- Actions — desktop only; mobile pakai floating CTA di bawah --}}
                        <div class="mt-5 space-y-2 hidden lg:block" x-data="{ isLoading: false }"
                             x-init="
                                if (!window.location.pathname.includes('/checkout')) {
                                    sessionStorage.removeItem('checkoutLoading');
                                }
                                window.addEventListener('checkout-page-ready', () => {
                                    isLoading = false;
                                    sessionStorage.removeItem('checkoutLoading');
                                });
                             ">
                            <button wire:click="checkout"
                                    wire:loading.attr="disabled"
                                    wire:target="checkout"
                                    @click="isLoading = true; sessionStorage.setItem('checkoutLoading', 'true')"
                                    class="w-full inline-flex items-center justify-center gap-2 grad-violet-emerald hover:opacity-95 active:opacity-90 text-white py-3 rounded-xl text-[14px] font-semibold transition-opacity shadow-card disabled:opacity-60 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="checkout" class="inline-flex items-center gap-2">
                                    Lanjut Checkout
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" d="M5 12h14"/><path stroke-linecap="round" d="m13 5 7 7-7 7"/></svg>
                                </span>
                                <span wire:loading wire:target="checkout">Memproses...</span>
                            </button>

                            {{-- Loading overlay --}}
                            <div x-show="isLoading" x-cloak
                                 x-transition.opacity.duration.200ms
                                 class="fixed inset-0 bg-black/55 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
                                <div class="bg-white rounded-2xl shadow-cardHover p-7 max-w-xs w-full text-center border border-black/[0.04]">
                                    <svg class="animate-spin h-10 w-10 text-emerald20-600 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <h3 class="font-display font-bold text-[15px] text-ink mb-1">Memuat Halaman</h3>
                                    <p class="text-[12px] text-black/55">Sedang memproses data...</p>
                                </div>
                            </div>
                        </div>

                        {{-- Security badge --}}
                        <div class="mt-5 pt-5 border-t border-black/5">
                            <div class="flex items-center justify-center gap-1.5 text-[11px] text-black/45">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                                <span>Transaksi aman dengan SSL</span>
                            </div>
                        </div>
                    </div>
                </aside>
            @endif
        </div>
    </div>

    <x-footer />

    {{-- ===== Mobile floating checkout CTA (lg:hidden) ===== --}}
    @if($cartItems->count() > 0)
        <div class="lg:hidden fixed inset-x-0 z-40 bottom-[60px] md:bottom-0 bg-white/95 backdrop-blur border-t border-black/10 shadow-[0_-8px_24px_rgba(0,0,0,0.08)]"
             x-data="{ isLoading: false }"
             x-init="
                if (!window.location.pathname.includes('/checkout')) sessionStorage.removeItem('checkoutLoading');
                window.addEventListener('checkout-page-ready', () => { isLoading = false; sessionStorage.removeItem('checkoutLoading'); });
             "
             style="padding-bottom: env(safe-area-inset-bottom);">
            <div class="container-x py-2.5 space-y-2">
                <div class="flex items-baseline justify-between gap-3">
                    <span class="text-[10px] text-black/55 uppercase tracking-wider font-semibold shrink-0">Total <span class="text-black/35">({{ $cartItems->count() }} item)</span>: {{ format_rupiah($total) }}</span>
                </div>
                <button wire:click="checkout"
                        wire:loading.attr="disabled"
                        wire:target="checkout"
                        @click="isLoading = true; sessionStorage.setItem('checkoutLoading', 'true')"
                        class="w-full inline-flex items-center justify-center gap-2 grad-violet-emerald hover:opacity-95 active:opacity-90 text-white py-3 rounded-xl text-[14px] font-semibold shadow-card transition-opacity disabled:opacity-60 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="checkout" class="inline-flex items-center gap-2">
                        Lanjut Checkout
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" d="M5 12h14"/><path stroke-linecap="round" d="m13 5 7 7-7 7"/></svg>
                    </span>
                    <span wire:loading wire:target="checkout">Memproses...</span>
                </button>
            </div>

            {{-- Loading overlay — di-teleport ke <body> supaya tidak terjebak containing block dari parent (backdrop-blur menciptakan containing block untuk fixed children) --}}
            <template x-teleport="body">
                <div x-show="isLoading" x-cloak
                     x-transition.opacity.duration.200ms
                     class="fixed inset-0 bg-black/55 backdrop-blur-sm z-[200] flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl shadow-cardHover p-7 max-w-xs w-full text-center border border-black/[0.04]">
                        <svg class="animate-spin h-10 w-10 text-emerald20-600 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <h3 class="font-display font-bold text-[15px] text-ink mb-1">Memuat Halaman</h3>
                        <p class="text-[12px] text-black/55">Sedang memproses data...</p>
                    </div>
                </div>
            </template>
        </div>
    @endif

    {{-- Toaster Notification --}}
    <div x-data="{
            notifications: [],
            add(message, type = 'success') {
                const id = Date.now();
                this.notifications.push({ id, message, type });
                setTimeout(() => this.remove(id), 3000);
            },
            remove(id) { this.notifications = this.notifications.filter(n => n.id !== id); }
         }"
         @notify.window="add($event.detail.message, $event.detail.type)"
         class="fixed top-24 right-4 z-[9999] flex flex-col gap-2 pointer-events-none">

        @if(session()->has('message'))
            <div x-init="add('{{ session('message') }}', 'success')"></div>
        @endif
        @if(session()->has('error'))
            <div x-init="add('{{ session('error') }}', 'error')"></div>
        @endif

        <template x-for="notification in notifications" :key="notification.id">
            <div x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-2"
                 class="pointer-events-auto inline-flex items-center gap-2 px-3 py-2.5 rounded-xl shadow-card text-[12px] font-medium border min-w-[220px] max-w-sm"
                 :class="{
                    'bg-white text-ink border-emerald20-200': notification.type === 'success',
                    'bg-sale/5 text-sale border-sale/20': notification.type === 'error'
                 }">
                <template x-if="notification.type === 'success'">
                    <svg class="w-4 h-4 text-emerald20-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </template>
                <template x-if="notification.type === 'error'">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                </template>
                <span class="flex-1" x-text="notification.message"></span>
                <button @click="remove(notification.id)" class="text-black/40 hover:text-ink p-0.5" aria-label="Tutup">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        </template>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</div>
