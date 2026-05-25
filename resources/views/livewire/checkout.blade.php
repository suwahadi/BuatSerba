<div class="bg-white"
     x-data="{ showPageLoading: sessionStorage.getItem('checkoutLoading') === 'true' }"
     x-init="
        window.addEventListener('checkout-page-ready', () => { showPageLoading = false; });
     ">

    {{-- Page loading overlay (teleport ke body supaya tidak terjebak containing block) --}}
    <template x-teleport="body">
        <div x-show="showPageLoading" x-cloak
             x-transition.opacity.duration.200ms
             class="fixed inset-0 z-[200] bg-black/55 backdrop-blur-sm flex items-center justify-center p-4">
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

    {{-- Branch Selection Modal --}}
    @if($showBranchModal && count($branches) > 0)
        <div class="fixed inset-0 z-[80] bg-black/55 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-cardHover max-w-md w-full p-5 sm:p-6 border border-black/[0.04]">
                <div class="text-center mb-5">
                    <div class="w-12 h-12 mx-auto rounded-full bg-emerald20-100 grid place-items-center mb-3">
                        <svg class="w-6 h-6 text-emerald20-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h2 class="font-display font-extrabold text-[18px] sm:text-[20px] text-ink">Pilih Cabang/Gudang</h2>
                    <p class="text-[12px] sm:text-[13px] text-black/55 mt-1">Lokasi ini akan digunakan untuk menghitung ongkos kirim</p>
                </div>
                <div class="space-y-2 max-h-80 overflow-y-auto">
                    @foreach($branches as $branch)
                        <button wire:click="selectBranch({{ $branch['id'] }})"
                                class="w-full p-3 sm:p-4 border-2 rounded-xl text-left transition-all
                                       {{ $selectedBranchId == $branch['id']
                                            ? 'border-emerald20-600 bg-emerald20-50'
                                            : 'border-black/10 hover:border-emerald20-500 hover:bg-emerald20-50' }}">
                            <p class="font-display font-semibold text-[14px] text-ink">{{ $branch['name'] }}</p>
                            <p class="text-[12px] text-emerald20-700 font-semibold mt-0.5">{{ $branch['city_name'] }}</p>
                            <p class="text-[11px] text-black/55 mt-1 leading-snug">{{ $branch['full_address'] }}</p>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <x-navbar :cartCount="$this->cartItems->count()" />

    {{-- Breadcrumb --}}
    <nav class="border-b border-black/5" aria-label="Breadcrumb">
        <div class="container-x py-2.5">
            <ol class="flex items-center gap-1.5 text-[12px]">
                <li><a href="/" class="text-black/55 hover:text-emerald20-600 transition-colors">Beranda</a></li>
                <li class="text-black/30">/</li>
                <li><a href="/cart" class="text-black/55 hover:text-emerald20-600 transition-colors">Keranjang</a></li>
                <li class="text-black/30">/</li>
                <li class="font-semibold text-ink">Checkout</li>
            </ol>
        </div>
    </nav>

    {{-- Page header --}}
    <header class="container-x pt-5 sm:pt-7 pb-4">
        <p class="text-[11px] uppercase tracking-[0.2em] text-tan5-600 font-semibold">Checkout · Langkah 2 dari 4</p>
        <h1 class="font-display font-extrabold text-[22px] md:text-[28px] tracking-tight text-ink mt-1">Detail Pengiriman &amp; Pembayaran</h1>
    </header>

    <div class="container-x pb-6 md:pb-10 lg:pb-14">
        {{-- Stepper --}}
        <div class="mb-6 sm:mb-8 overflow-x-auto no-scrollbar">
            <ol class="flex items-center justify-center gap-2 sm:gap-3 md:gap-4 min-w-max">
                @php
                    $steps = [
                        ['n' => 1, 'label' => 'Keranjang',  'state' => 'done'],
                        ['n' => 2, 'label' => 'Checkout',   'state' => 'active'],
                        ['n' => 3, 'label' => 'Pembayaran', 'state' => 'pending'],
                        ['n' => 4, 'label' => 'Selesai',    'state' => 'pending'],
                    ];
                @endphp
                @foreach($steps as $i => $step)
                    <li class="flex items-center gap-2 sm:gap-3 shrink-0">
                        <span class="grid place-items-center w-7 h-7 sm:w-8 sm:h-8 rounded-full text-[12px] sm:text-[13px] font-mono font-bold
                            @if($step['state'] === 'done') bg-emerald20-600 text-white
                            @elseif($step['state'] === 'active') bg-emerald20-600 text-white shadow-card
                            @else bg-paper border border-black/10 text-black/45
                            @endif">
                            @if($step['state'] === 'done')
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @else
                                {{ $step['n'] }}
                            @endif
                        </span>
                        <span class="text-[11px] sm:text-[12px] font-semibold uppercase tracking-wider hidden sm:inline
                            {{ in_array($step['state'], ['done', 'active']) ? 'text-ink' : 'text-black/45' }}">
                            {{ $step['label'] }}
                        </span>
                    </li>
                    @if($i < count($steps) - 1)
                        <li class="w-6 sm:w-10 md:w-14 h-px shrink-0 {{ $step['state'] === 'done' ? 'bg-emerald20-600' : 'bg-black/10' }}" aria-hidden="true"></li>
                    @endif
                @endforeach
            </ol>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

            {{-- ===== Form Sections ===== --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Informasi Pembeli --}}
                <section class="bg-white rounded-2xl shadow-card border border-black/[0.04] p-5 sm:p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="grid place-items-center w-7 h-7 rounded-full bg-emerald20-100 text-emerald20-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        <h2 class="font-display font-bold text-[15px] sm:text-[16px] text-ink">Informasi Pembeli</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                        <label class="block md:col-span-2 sm:col-span-1">
                            <span class="text-[11px] text-black/55 uppercase tracking-wider font-semibold">Nama Lengkap *</span>
                            <input type="text" wire:model="fullName"
                                   class="w-full mt-1 px-3 py-2 bg-paper border rounded-lg text-[13px] text-ink focus:outline-none focus:border-emerald20-500 focus:bg-white transition-colors @error('fullName') border-sale/50 @else border-black/10 @enderror">
                            @error('fullName') <span class="text-sale text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </label>
                        <label class="block">
                            <span class="text-[11px] text-black/55 uppercase tracking-wider font-semibold">Nomor Telepon *</span>
                            <input type="tel" wire:model="phone"
                                   class="w-full mt-1 px-3 py-2 bg-paper border rounded-lg text-[13px] font-mono text-ink focus:outline-none focus:border-emerald20-500 focus:bg-white transition-colors @error('phone') border-sale/50 @else border-black/10 @enderror">
                            @error('phone') <span class="text-sale text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </label>
                        <label class="block">
                            <span class="text-[11px] text-black/55 uppercase tracking-wider font-semibold">Email *</span>
                            <input type="email" wire:model="email"
                                   class="w-full mt-1 px-3 py-2 bg-paper border rounded-lg text-[13px] text-ink focus:outline-none focus:border-emerald20-500 focus:bg-white transition-colors @error('email') border-sale/50 @else border-black/10 @enderror">
                            @error('email') <span class="text-sale text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </label>
                    </div>
                </section>

                {{-- Alamat Pengiriman --}}
                <section class="bg-white rounded-2xl shadow-card border border-black/[0.04] p-5 sm:p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="grid place-items-center w-7 h-7 rounded-full bg-emerald20-100 text-emerald20-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <h2 class="font-display font-bold text-[15px] sm:text-[16px] text-ink">Alamat Pengiriman</h2>
                    </div>

                    <label class="block mb-3">
                        <span class="text-[11px] text-black/55 uppercase tracking-wider font-semibold">Alamat Lengkap *</span>
                        <textarea wire:model="address" rows="3" placeholder="Nama jalan, nomor rumah, RT/RW, patokan yang jelas"
                                  class="w-full mt-1 px-3 py-2 bg-paper border rounded-lg text-[13px] text-ink focus:outline-none focus:border-emerald20-500 focus:bg-white transition-colors resize-none @error('address') border-sale/50 @else border-black/10 @enderror"></textarea>
                        @error('address') <span class="text-sale text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                        {{-- Province --}}
                        <label class="block">
                            <span class="text-[11px] text-black/55 uppercase tracking-wider font-semibold">Provinsi *</span>
                            <div class="relative mt-1">
                                <select wire:model.live="provinceId"
                                        class="w-full px-3 py-2 bg-paper border rounded-lg text-[13px] text-ink focus:outline-none focus:border-emerald20-500 focus:bg-white transition-colors appearance-none pr-8 @error('provinceId') border-sale/50 @else border-black/10 @enderror">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach($provinces as $id => $name)
                                        <option value="{{ $id }}" wire:key="province-{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none text-black/40">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m6 9 6 6 6-6"/></svg>
                                </div>
                                <div wire:loading wire:target="provinceId" class="absolute inset-y-0 right-7 flex items-center">
                                    <svg class="animate-spin h-4 w-4 text-emerald20-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>
                            </div>
                            @error('provinceId') <span class="text-sale text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </label>

                        {{-- City --}}
                        <label class="block">
                            <span class="text-[11px] text-black/55 uppercase tracking-wider font-semibold">Kota/Kabupaten *</span>
                            <div class="relative mt-1">
                                <select wire:model.live="cityId"
                                        {{ empty($cities) ? 'disabled' : '' }}
                                        class="w-full px-3 py-2 bg-paper border rounded-lg text-[13px] text-ink focus:outline-none focus:border-emerald20-500 focus:bg-white transition-colors appearance-none pr-8 disabled:opacity-50 @error('cityId') border-sale/50 @else border-black/10 @enderror">
                                    <option value="">Pilih Kota/Kabupaten</option>
                                    @foreach($cities as $id => $name)
                                        <option value="{{ $id }}" wire:key="city-{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none text-black/40">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m6 9 6 6 6-6"/></svg>
                                </div>
                                <div wire:loading wire:target="cityId" class="absolute inset-y-0 right-7 flex items-center">
                                    <svg class="animate-spin h-4 w-4 text-emerald20-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>
                                <div wire:loading wire:target="provinceId" class="absolute inset-0 bg-white/75 grid place-items-center rounded-lg">
                                    <svg class="animate-spin h-4 w-4 text-emerald20-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>
                            </div>
                            @error('cityId') <span class="text-sale text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </label>

                        {{-- District --}}
                        <label class="block">
                            <span class="text-[11px] text-black/55 uppercase tracking-wider font-semibold">Kecamatan *</span>
                            <div class="relative mt-1">
                                <select wire:model.live="districtId"
                                        {{ empty($districts) ? 'disabled' : '' }}
                                        class="w-full px-3 py-2 bg-paper border rounded-lg text-[13px] text-ink focus:outline-none focus:border-emerald20-500 focus:bg-white transition-colors appearance-none pr-8 disabled:opacity-50 @error('districtId') border-sale/50 @else border-black/10 @enderror">
                                    <option value="">Pilih Kecamatan</option>
                                    @foreach($districts as $id => $name)
                                        <option value="{{ $id }}" wire:key="district-{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none text-black/40">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m6 9 6 6 6-6"/></svg>
                                </div>
                                <div wire:loading wire:target="districtId" class="absolute inset-y-0 right-7 flex items-center">
                                    <svg class="animate-spin h-4 w-4 text-emerald20-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>
                                <div wire:loading wire:target="cityId" class="absolute inset-0 bg-white/75 grid place-items-center rounded-lg">
                                    <svg class="animate-spin h-4 w-4 text-emerald20-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>
                            </div>
                            @error('districtId') <span class="text-sale text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </label>

                        {{-- Subdistrict --}}
                        <label class="block">
                            <span class="text-[11px] text-black/55 uppercase tracking-wider font-semibold">Kelurahan *</span>
                            <div class="relative mt-1">
                                <select wire:model.live="subdistrictId"
                                        {{ empty($subdistricts) ? 'disabled' : '' }}
                                        class="w-full px-3 py-2 bg-paper border rounded-lg text-[13px] text-ink focus:outline-none focus:border-emerald20-500 focus:bg-white transition-colors appearance-none pr-8 disabled:opacity-50 @error('subdistrictId') border-sale/50 @else border-black/10 @enderror">
                                    <option value="">Pilih Kelurahan</option>
                                    @foreach($subdistricts as $id => $name)
                                        <option value="{{ $id }}" wire:key="subdistrict-{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none text-black/40">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m6 9 6 6 6-6"/></svg>
                                </div>
                                <div wire:loading wire:target="subdistrictId" class="absolute inset-y-0 right-7 flex items-center">
                                    <svg class="animate-spin h-4 w-4 text-emerald20-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>
                                <div wire:loading wire:target="districtId" class="absolute inset-0 bg-white/75 grid place-items-center rounded-lg">
                                    <svg class="animate-spin h-4 w-4 text-emerald20-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>
                            </div>
                            @error('subdistrictId') <span class="text-sale text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </label>

                        {{-- Postal code --}}
                        <label class="block">
                            <span class="text-[11px] text-black/55 uppercase tracking-wider font-semibold">Kode Pos *</span>
                            <input type="text" wire:model="postalCode"
                                   class="w-full mt-1 px-3 py-2 bg-paper border rounded-lg text-[13px] font-mono text-ink focus:outline-none focus:border-emerald20-500 focus:bg-white transition-colors @error('postalCode') border-sale/50 @else border-black/10 @enderror">
                            @error('postalCode') <span class="text-sale text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </label>
                    </div>
                </section>

                {{-- Metode Pengiriman --}}
                <section class="bg-white rounded-2xl shadow-card border border-black/[0.04] p-5 sm:p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="grid place-items-center w-7 h-7 rounded-full bg-emerald20-100 text-emerald20-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/>
                                <path d="M15 18H9"/>
                                <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/>
                                <circle cx="17" cy="18" r="2"/>
                                <circle cx="7" cy="18" r="2"/>
                            </svg>
                        </span>
                        <h2 class="font-display font-bold text-[15px] sm:text-[16px] text-ink">Metode Pengiriman</h2>
                    </div>

                    @if($this->selectedBranch && count($branches) > 1)
                        <div class="bg-violet10-50 border border-violet10-100 rounded-xl p-3 mb-4">
                            <div class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-violet10-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[12px] sm:text-[13px] font-semibold text-violet10-800">Dikirim dari: {{ $this->selectedBranch->name }}</p>
                                    <p class="text-[11px] text-violet10-700 mt-0.5">{{ $this->selectedBranch->city_name }}</p>
                                    <button wire:click="$set('showBranchModal', true)" class="mt-1.5 inline-flex items-center gap-1 text-[11px] text-violet10-600 hover:text-violet10-800 font-semibold underline-offset-2 hover:underline">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Ubah cabang
                                    </button>
                                </div>
                                @if($subdistrictId)
                                    <div wire:loading wire:target="selectBranch">
                                        <svg class="animate-spin h-4 w-4 text-violet10-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if(empty($districtId))
                        <div class="bg-paper/60 border border-black/5 rounded-xl text-center py-8 px-5">
                            <svg class="w-10 h-10 mx-auto text-black/30 mb-2" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-[12px] sm:text-[13px] text-black/55">Lengkapi alamat pengiriman untuk melihat opsi pengiriman</p>
                        </div>
                    @else
                        <div wire:loading wire:target="districtId" class="flex flex-col items-center justify-center py-8">
                            <svg class="animate-spin h-8 w-8 text-emerald20-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-[13px] text-ink font-semibold">Menghitung ongkos kirim...</p>
                            <p class="text-[11px] text-black/55 mt-0.5">Mohon tunggu sebentar</p>
                        </div>

                        <div wire:loading.remove wire:target="districtId">
                            <div class="grid grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3">
                                @forelse($shippingMethods as $method)
                                    @php $isSel = $shippingMethod === $method['id']; @endphp
                                    <label wire:key="shipping-{{ $method['id'] }}"
                                           class="flex flex-col p-3 border-2 rounded-xl cursor-pointer transition-all
                                                  {{ $isSel ? 'border-emerald20-600 bg-emerald20-50' : 'border-black/10 hover:border-emerald20-300' }}">
                                        <div class="flex items-start gap-2">
                                            <input type="radio" wire:model.live="shippingMethod" value="{{ $method['id'] }}"
                                                   class="mt-0.5 w-4 h-4 text-emerald20-600 border-black/20 focus:ring-emerald20-500 focus:ring-2 focus:ring-offset-0 shrink-0">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[13px] font-semibold text-ink leading-tight">{{ $method['name'] }}</p>
                                                <p class="text-[11px] text-black/55 mt-0.5 line-clamp-2">{{ $method['description'] }}</p>
                                            </div>
                                        </div>
                                        <div class="pl-6 mt-2 flex items-center justify-between">
                                            <p class="text-[11px] text-black/55">
                                                Estimasi: <span class="font-mono text-ink/80">{{ preg_replace('/\s*(day|days)\s*/i', '', $method['estimatedDays']) }} hari</span>
                                            </p>
                                            <p class="font-mono font-extrabold text-[13px] text-emerald20-700">{{ format_rupiah($method['cost']) }}</p>
                                        </div>
                                    </label>
                                @empty
                                    <div class="col-span-full bg-paper/60 border border-black/5 rounded-xl text-center py-8 px-5">
                                        <svg class="w-10 h-10 mx-auto text-black/30 mb-2" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                        <h3 class="font-display font-bold text-[14px] text-ink">Metode pengiriman tidak tersedia</h3>
                                        <p class="text-[12px] text-black/55 mt-1">Silakan lengkapi alamat pengiriman untuk melihat opsi pengiriman.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        @error('shippingMethod') <span class="text-sale text-[11px] mt-3 block">{{ $message }}</span> @enderror
                    @endif
                </section>

                {{-- Metode Pembayaran --}}
                <section class="bg-white rounded-2xl shadow-card border border-black/[0.04] p-5 sm:p-6">
                    <div class="flex items-center gap-2 mb-5">
                        <span class="grid place-items-center w-7 h-7 rounded-full bg-emerald20-100 text-emerald20-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </span>
                        <h2 class="font-display font-bold text-[15px] sm:text-[16px] text-ink">Metode Pembayaran</h2>
                    </div>

                    @php
                        $vaIds = ['bank-transfer-bca', 'bank-transfer-bni', 'bank-transfer-bri', 'bank-transfer-mandiri'];
                        $ewalletIds = ['qris', 'e-wallet-gopay'];
                        $vaMethods = collect($this->paymentMethods)->filter(fn($m) => in_array($m['id'], $vaIds));
                        $ewalletMethods = collect($this->paymentMethods)->filter(fn($m) => in_array($m['id'], $ewalletIds));
                        $otherMethods = collect($this->paymentMethods)->filter(fn($m) => !in_array($m['id'], array_merge($vaIds, $ewalletIds)));
                        $logoMap = [
                            'bank-transfer-bca' => '/storage/static/bca.png',
                            'bank-transfer-bni' => '/storage/static/bni.png',
                            'bank-transfer-bri' => '/storage/static/bri.png',
                            'bank-transfer-mandiri' => '/storage/static/mandiri.jpg',
                            'qris' => '/storage/static/qris.png',
                            'e-wallet-gopay' => '/storage/static/gopay.png',
                        ];
                    @endphp

                    <div class="space-y-5">

                        {{-- Virtual Account --}}
                        @if($vaMethods->count() > 0)
                            <div>
                                <div class="flex items-center gap-2 mb-2.5">
                                    <span class="font-display font-semibold text-[11px] text-ink uppercase tracking-[0.12em]">Virtual Account</span>
                                    <span class="h-px flex-1 bg-black/5"></span>
                                </div>
                                <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                                    @foreach($vaMethods as $method)
                                        @php
                                            $isSel = $paymentMethod === $method['id'];
                                            $logoUrl = $logoMap[$method['id']] ?? null;
                                        @endphp
                                        <label wire:key="pay-{{ $method['id'] }}"
                                               class="relative flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition-all
                                                      {{ $isSel ? 'border-emerald20-600 bg-emerald20-50' : 'border-black/10 hover:border-emerald20-300 hover:bg-paper' }}">
                                            <input type="radio" wire:model.live="paymentMethod" value="{{ $method['id'] }}" class="sr-only">
                                            <div class="w-12 h-12 bg-white rounded-lg grid place-items-center p-1.5 border border-black/[0.04] shrink-0">
                                                @if($logoUrl)
                                                    <img src="{{ $logoUrl }}" alt="{{ $method['name'] }}" class="w-full h-full object-contain">
                                                @else
                                                    <span class="text-[10px] font-bold text-black/60">{{ strtoupper(substr($method['name'], 0, 3)) }}</span>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[13px] font-semibold text-ink leading-tight">{{ $method['name'] }}</p>
                                                <p class="text-[11px] text-black/55 mt-0.5 line-clamp-2">{{ $method['description'] }}</p>
                                            </div>
                                            @if($isSel)
                                                <span class="absolute top-1.5 right-1.5 grid place-items-center w-5 h-5 rounded-full bg-emerald20-600 text-white">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                </span>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- E-Wallet & QRIS --}}
                        @if($ewalletMethods->count() > 0)
                            <div>
                                <div class="flex items-center gap-2 mb-2.5">
                                    <span class="font-display font-semibold text-[11px] text-ink uppercase tracking-[0.12em]">E-Wallet &amp; QRIS</span>
                                    <span class="h-px flex-1 bg-black/5"></span>
                                </div>
                                <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                                    @foreach($ewalletMethods as $method)
                                        @php
                                            $isSel = $paymentMethod === $method['id'];
                                            $logoUrl = $logoMap[$method['id']] ?? null;
                                        @endphp
                                        <label wire:key="pay-{{ $method['id'] }}"
                                               class="relative flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition-all
                                                      {{ $isSel ? 'border-emerald20-600 bg-emerald20-50' : 'border-black/10 hover:border-emerald20-300 hover:bg-paper' }}">
                                            <input type="radio" wire:model.live="paymentMethod" value="{{ $method['id'] }}" class="sr-only">
                                            <div class="w-12 h-12 bg-white rounded-lg grid place-items-center p-1.5 border border-black/[0.04] shrink-0">
                                                @if($logoUrl)
                                                    <img src="{{ $logoUrl }}" alt="{{ $method['name'] }}" class="w-full h-full object-contain">
                                                @else
                                                    <span class="text-[10px] font-bold text-black/60">{{ strtoupper(substr($method['name'], 0, 3)) }}</span>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[13px] font-semibold text-ink leading-tight">{{ $method['name'] }}</p>
                                                <p class="text-[11px] text-black/55 mt-0.5 line-clamp-2">{{ $method['description'] }}</p>
                                            </div>
                                            @if($isSel)
                                                <span class="absolute top-1.5 right-1.5 grid place-items-center w-5 h-5 rounded-full bg-emerald20-600 text-white">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                </span>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Other methods --}}
                        @if($otherMethods->count() > 0)
                            <div>
                                <div class="flex items-center gap-2 mb-2.5">
                                    <span class="font-display font-semibold text-[11px] text-ink uppercase tracking-[0.12em]">Metode Lainnya</span>
                                    <span class="h-px flex-1 bg-black/5"></span>
                                </div>
                                <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                                    @foreach($otherMethods as $method)
                                        @php
                                            $isMemberBalance = $method['id'] === 'member_balance';
                                            $isDisabled = $isMemberBalance && !$isPremiumMember;
                                            $isSel = $paymentMethod === $method['id'];
                                        @endphp
                                        <label wire:key="pay-{{ $method['id'] }}"
                                               class="relative flex items-start gap-3 p-3 border-2 rounded-xl transition-all
                                                      @if($isDisabled) bg-paper/40 border-black/5 text-black/40 cursor-not-allowed
                                                      @elseif($isSel) border-emerald20-600 bg-emerald20-50 cursor-pointer
                                                      @else border-black/10 hover:border-emerald20-300 hover:bg-paper cursor-pointer
                                                      @endif">
                                            <input type="radio" wire:model.live="paymentMethod" value="{{ $method['id'] }}"
                                                   class="mt-0.5 w-4 h-4 text-emerald20-600 border-black/20 focus:ring-emerald20-500 focus:ring-2 focus:ring-offset-0 shrink-0"
                                                   @if($isDisabled) disabled @endif>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <p class="text-[13px] font-semibold leading-tight {{ $isDisabled ? 'text-black/40' : 'text-ink' }}">{{ $method['name'] }}</p>
                                                    @if($method['id'] === 'cod' && !$isDisabled)
                                                        <span class="inline-block bg-violet10-100 text-violet10-700 text-[10px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wider">Populer</span>
                                                    @endif
                                                    @if($isMemberBalance && !$isPremiumMember)
                                                        <span class="inline-block bg-tan5-100 text-tan5-700 text-[10px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wider">Khusus Premium</span>
                                                    @endif
                                                </div>
                                                <p class="text-[11px] mt-0.5 leading-snug {{ $isDisabled ? 'text-black/40' : 'text-black/55' }}">{{ $method['description'] }}</p>
                                            </div>
                                            @if($isSel)
                                                <span class="absolute top-1.5 right-1.5 grid place-items-center w-5 h-5 rounded-full bg-emerald20-600 text-white">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                </span>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    @error('paymentMethod') <span class="text-sale text-[11px] mt-3 block">{{ $message }}</span> @enderror
                </section>
            </div>

            {{-- ===== Order Summary ===== --}}
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-card border border-black/[0.04] p-5 sm:p-6 sticky top-24">
                    <h3 class="font-display font-bold text-[16px] text-ink mb-4">Ringkasan Pesanan</h3>

                    {{-- Cart items mini list --}}
                    <div class="space-y-3 mb-4 max-h-60 overflow-y-auto -mx-1 px-1">
                        @foreach($this->cartItems as $item)
                            @php
                                $variantName = null;
                                $attrs = $item->sku->attributes ?? [];
                                if (! empty($attrs['name']) && is_string($attrs['name']) && trim($attrs['name']) !== '') {
                                    $variantName = $attrs['name'];
                                } else {
                                    foreach ($attrs as $k => $v) {
                                        if ($k === 'image' || $v === null) continue;
                                        if (is_string($v) && trim($v) === '') continue;
                                        $variantName = $v; break;
                                    }
                                }
                            @endphp
                            <div class="flex items-start gap-2.5 pb-3 border-b border-black/5 last:border-0 last:pb-0" wire:key="summary-item-{{ $item->id }}">
                                <img src="{{ image_url($item->sku->image ?? $item->product->main_image) }}" alt="{{ $item->product->name }}"
                                     class="w-12 h-12 object-cover rounded-lg bg-paper border border-black/[0.04] shrink-0"
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2780%27 height=%2780%27%3E%3Crect width=%2780%27 height=%2780%27 fill=%27%23f5f5f0%27/%3E%3C/svg%3E'">
                                <div class="flex-1 min-w-0">
                                    <p class="text-[12px] font-semibold text-ink line-clamp-2 leading-tight">{{ $item->product->name }}</p>
                                    @if($variantName)
                                        <p class="text-[10px] text-black/55 mt-0.5">{{ $variantName }}</p>
                                    @endif
                                    <p class="text-[11px] font-mono text-black/55 mt-0.5">{{ $item->quantity }} × {{ format_rupiah($item->price) }}</p>
                                </div>
                                <p class="text-[12px] font-mono font-semibold text-ink whitespace-nowrap">{{ format_rupiah($item->price * $item->quantity) }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Breakdown --}}
                    <dl class="space-y-2 mb-4 text-[13px] pt-4 border-t border-black/5">
                        <div class="flex items-center justify-between">
                            <dt class="text-black/60">Subtotal <span class="text-black/40">({{ $this->cartItems->count() }} item)</span></dt>
                            <dd class="font-mono font-semibold text-ink">{{ format_rupiah($this->subtotal) }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-black/60">Ongkos Kirim</dt>
                            <dd class="font-mono font-semibold text-ink">
                                @if($shippingCost > 0 || ($shippingCost == 0 && $this->selectedShippingOriginalCost > 0))
                                    @if($shippingCost < $this->selectedShippingOriginalCost)
                                        <span class="text-black/40 line-through mr-1 text-[11px]">{{ format_rupiah($this->selectedShippingOriginalCost) }}</span>
                                        <span class="text-emerald20-700">{{ format_rupiah($shippingCost) }}</span>
                                    @else
                                        {{ format_rupiah($shippingCost) }}
                                    @endif
                                @else
                                    <span class="text-[11px] italic text-black/40">Menunggu data</span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-black/60">Biaya Layanan</dt>
                            <dd class="font-mono font-semibold text-ink">{{ format_rupiah($serviceFee) }}</dd>
                        </div>
                        @if($discount > 0)
                            <div class="flex items-center justify-between bg-emerald20-50 border border-emerald20-100 rounded-lg px-2.5 py-2">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <svg class="w-3.5 h-3.5 text-emerald20-700 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    <span class="text-[11px] font-semibold text-emerald20-800 truncate">Voucher · <span class="font-mono">{{ strtoupper($voucherCode) }}</span></span>
                                </div>
                                <span class="font-mono font-bold text-emerald20-700 text-[12px] shrink-0">−{{ format_rupiah($discount) }}</span>
                            </div>
                        @endif
                    </dl>

                    {{-- Total --}}
                    <div class="pt-4 mt-4 border-t border-black/5">
                        <div class="flex items-baseline justify-between">
                            <span class="font-display font-bold text-[14px] text-ink">Total</span>
                            <span class="font-mono font-extrabold text-[20px] sm:text-[22px] text-emerald20-700">{{ format_rupiah($this->total) }}</span>
                        </div>
                    </div>

                    {{-- Desktop actions --}}
                    <div class="mt-5 space-y-2 hidden lg:block">
                        <button wire:click="placeOrder" wire:loading.attr="disabled" wire:target="placeOrder"
                                class="w-full inline-flex items-center justify-center gap-2 grad-violet-emerald hover:opacity-95 active:opacity-90 text-white py-3 rounded-xl text-[14px] font-semibold transition-opacity shadow-card disabled:opacity-60 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="placeOrder" class="inline-flex items-center gap-2">
                                Buat Pesanan
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" d="M5 12h14"/><path stroke-linecap="round" d="m13 5 7 7-7 7"/></svg>
                            </span>
                            <span wire:loading wire:target="placeOrder">Memproses...</span>
                        </button>
                        <a href="/cart" class="block w-full text-center border border-black/10 text-ink/75 hover:border-emerald20-500 hover:text-emerald20-700 py-2.5 rounded-xl text-[13px] font-semibold transition-colors">
                            Kembali ke Keranjang
                        </a>
                    </div>

                    {{-- Security badge --}}
                    <div class="mt-5 pt-5 border-t border-black/5">
                        <div class="flex items-center justify-center gap-1.5 text-[11px] text-black/45">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                            <span>Transaksi aman &amp; terenkripsi</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <x-footer />

    {{-- ===== Mobile floating CTA (lg:hidden) ===== --}}
    <div class="lg:hidden fixed inset-x-0 z-40 bottom-[60px] md:bottom-0 bg-white/95 backdrop-blur border-t border-black/10 shadow-[0_-8px_24px_rgba(0,0,0,0.08)]"
         style="padding-bottom: env(safe-area-inset-bottom);">
        <div class="container-x py-2.5 space-y-2">
            <div class="flex items-baseline justify-between gap-3">
                <span class="text-[10px] text-black/55 uppercase tracking-wider font-semibold shrink-0">Total <span class="text-black/35">({{ $this->cartItems->count() }} item)</span>: {{ format_rupiah($this->total) }}</span>
            </div>
            <button wire:click="placeOrder" wire:loading.attr="disabled" wire:target="placeOrder"
                    class="w-full inline-flex items-center justify-center gap-2 grad-violet-emerald hover:opacity-95 active:opacity-90 text-white py-3 rounded-xl text-[14px] font-semibold shadow-card transition-opacity disabled:opacity-60 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="placeOrder" class="inline-flex items-center gap-2">
                    Buat Pesanan
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" d="M5 12h14"/><path stroke-linecap="round" d="m13 5 7 7-7 7"/></svg>
                </span>
                <span wire:loading wire:target="placeOrder">Memproses...</span>
            </button>
        </div>
    </div>

    {{-- ===== Payment Error Modal ===== --}}
    <div x-data="{
            showError: false, errorTitle: '', errorMessage: '',
            closeAndReload() { this.showError = false; setTimeout(() => window.location.reload(), 250); }
         }"
         x-on:show-payment-error.window="showError = true; errorTitle = $event.detail.title; errorMessage = $event.detail.message"
         x-show="showError" x-cloak
         class="fixed inset-0 z-[100] overflow-y-auto">
        <div class="fixed inset-0 bg-black/55 backdrop-blur-sm"
             x-show="showError" x-transition.opacity.duration.200ms></div>
        <div class="flex min-h-full items-center justify-center p-4 relative">
            <div class="relative bg-white rounded-2xl shadow-cardHover max-w-md w-full border border-black/[0.04]"
                 x-show="showError"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 @click.outside="closeAndReload()">
                <div class="p-6 text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-sale/10 grid place-items-center mb-3">
                        <svg class="w-7 h-7 text-sale" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-[17px] text-ink" x-text="errorTitle"></h3>
                    <p class="mt-2 text-[13px] text-black/65 leading-relaxed" x-text="errorMessage"></p>
                    <div class="mt-4 p-3 bg-tan5-50 border border-tan5-100 rounded-lg text-left">
                        <p class="text-[12px] text-tan5-700"><span class="font-semibold">Tips:</span> Coba pilih metode pembayaran lain atau hubungi kami jika masalah berlanjut.</p>
                    </div>
                    <button @click="closeAndReload()" class="mt-5 w-full grad-violet-emerald hover:opacity-95 text-white font-semibold py-2.5 rounded-xl text-[13px] shadow-card transition-opacity">
                        Saya Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Payment Success Modal ===== --}}
    <div x-data="{ showSuccess: false, successTitle: '', successMessage: '' }"
         x-on:show-payment-success.window="showSuccess = true; successTitle = $event.detail.title; successMessage = $event.detail.message"
         x-show="showSuccess" x-cloak
         class="fixed inset-0 z-[100] overflow-y-auto">
        <div class="fixed inset-0 bg-black/55 backdrop-blur-sm" x-show="showSuccess" x-transition.opacity.duration.200ms></div>
        <div class="flex min-h-full items-center justify-center p-4 relative">
            <div class="relative bg-white rounded-2xl shadow-cardHover max-w-md w-full border border-black/[0.04]"
                 x-show="showSuccess"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                <div class="p-6 text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-emerald20-100 grid place-items-center mb-3">
                        <svg class="w-7 h-7 text-emerald20-700" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-[17px] text-ink" x-text="successTitle"></h3>
                    <p class="mt-2 text-[13px] text-black/65 leading-relaxed" x-text="successMessage"></p>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>

    <script>
        document.addEventListener('livewire:init', function() {
            Livewire.on('validation-failed', () => {
                setTimeout(() => {
                    const firstError = document.querySelector('.border-sale\\/50, .text-sale');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        if (['INPUT', 'SELECT', 'TEXTAREA'].includes(firstError.tagName)) firstError.focus();
                    }
                }, 100);
            });

            Livewire.on('showModalError', (data) => {
                window.dispatchEvent(new CustomEvent('show-payment-error', {
                    detail: { title: data[0].title, message: data[0].message }
                }));
            });
            Livewire.on('showModalSuccess', (data) => {
                window.dispatchEvent(new CustomEvent('show-payment-success', {
                    detail: { title: data[0].title, message: data[0].message }
                }));
            });
        });
    </script>
</div>

<script>
    // Dispatch event when checkout page is ready (clears the page-load overlay)
    document.addEventListener('livewire:init', () => {
        Livewire.hook('morph.updated', () => {
            setTimeout(() => {
                if (window.location.pathname.includes('/checkout')) {
                    window.dispatchEvent(new CustomEvent('checkout-page-ready'));
                    sessionStorage.removeItem('checkoutLoading');
                }
            }, 1200);
        });
    });

    window.addEventListener('load', () => {
        if (window.location.pathname.includes('/checkout')) {
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('checkout-page-ready'));
                sessionStorage.removeItem('checkoutLoading');
            }, 800);
        }
    });
</script>
