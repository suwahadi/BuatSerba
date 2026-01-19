<div class="bg-gray-50">
    <!-- Branch Selection Modal -->
    @if($showBranchModal && count($branches) > 0)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: flex;">
        <div class="bg-white rounded-lg shadow-2xl max-w-md w-full p-6">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Pilih Cabang/Gudang</h2>
                <p class="text-gray-600 mt-2">Lokasi ini akan digunakan untuk menghitung ongkos kirim</p>
            </div>
            
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach($branches as $branch)
                <button 
                    wire:click="selectBranch({{ $branch['id'] }})" 
                    class="w-full p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all text-left {{ $selectedBranchId == $branch['id'] ? 'border-green-600 bg-green-50' : '' }}">
                    <p class="font-semibold text-gray-900">{{ $branch['name'] }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $branch['city_name'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $branch['full_address'] }}</p>
                </button>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    <!-- Navigation -->
    <x-navbar :cartCount="$this->cartItems->count()" />

    <!-- Breadcrumb -->
    <div class="pt-16 sm:pt-20 pb-3 sm:pb-4 bg-white border-b">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm">
                    <li><a href="/" class="text-gray-500 hover:text-gray-700">Beranda</a></li>
                    <li><span class="text-gray-400">/</span></li>
                    <li><a href="/cart" class="text-gray-500 hover:text-gray-700">Keranjang</a></li>
                    <li><span class="text-gray-400">/</span></li>
                    <li><span class="text-gray-900 font-medium">Checkout</span></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8">
        <!-- Checkout Steps -->
        <div class="mb-4 sm:mb-8 overflow-x-auto">
            <div class="flex items-center justify-center space-x-2 sm:space-x-4 md:space-x-8 min-w-max px-4">
                <div class="flex items-center space-x-1 sm:space-x-2">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold bg-green-600 text-white">1</div>
                    <span class="text-xs sm:text-sm font-medium hidden sm:inline">Keranjang</span>
                </div>
                <div class="w-8 sm:w-16 h-0.5 sm:h-1 bg-green-600"></div>
                <div class="flex items-center space-x-1 sm:space-x-2">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold bg-green-600 text-white">2</div>
                    <span class="text-xs sm:text-sm font-medium hidden sm:inline">Checkout</span>
                </div>
                <div class="w-8 sm:w-16 h-0.5 sm:h-1 bg-gray-200"></div>
                <div class="flex items-center space-x-1 sm:space-x-2">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold bg-gray-200 text-gray-600">3</div>
                    <span class="text-xs sm:text-sm font-medium text-gray-600 hidden sm:inline">Pembayaran</span>
                </div>
                <div class="w-8 sm:w-16 h-0.5 sm:h-1 bg-gray-200"></div>
                <div class="flex items-center space-x-1 sm:space-x-2">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold bg-gray-200 text-gray-600">4</div>
                    <span class="text-xs sm:text-sm font-medium text-gray-600 hidden sm:inline">Selesai</span>
                </div>
            </div>
        </div>

        <!-- Checkout Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                <!-- Customer Information -->
                <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
                    <h2 class="text-base sm:text-xl font-bold text-gray-900 mb-3 sm:mb-4 flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Informasi Pembeli
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Nama Lengkap *</label>
                            <input type="text" wire:model="fullName" 
                                   class="w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 @error('fullName') border-red-500 @enderror">
                            @error('fullName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Nomor Telepon *</label>
                            <input type="tel" wire:model="phone" 
                                   class="w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 @error('phone') border-red-500 @enderror">
                            @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Email *</label>
                            <input type="email" wire:model="email" 
                                   class="w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 @error('email') border-red-500 @enderror">
                            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
                    <h2 class="text-base sm:text-xl font-bold text-gray-900 mb-3 sm:mb-4 flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Alamat Pengiriman
                    </h2>
                    
                    <div class="mb-3 sm:mb-4">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Alamat Lengkap *</label>
                        <textarea wire:model="address" rows="3" 
                                  placeholder="Nama jalan, nomor rumah, RT/RW, patokan yang jelas"
                                  class="w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 @error('address') border-red-500 @enderror"></textarea>
                        @error('address') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Provinsi *</label>
                            <div class="relative">
                                <select wire:model.live="provinceId" 
                                        class="w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 @error('provinceId') border-red-500 @enderror">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach($provinces as $id => $name)
                                    <option value="{{ $id }}" wire:key="province-{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div wire:loading wire:target="provinceId" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="animate-spin h-4 w-4 sm:h-5 sm:w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('provinceId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Kota/Kabupaten *</label>
                            <div class="relative">
                                <select wire:model.live="cityId" 
                                        class="w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 @error('cityId') border-red-500 @enderror"
                                        {{ empty($cities) ? 'disabled' : '' }}>
                                    <option value="">Pilih Kota/Kabupaten</option>
                                    @foreach($cities as $id => $name)
                                    <option value="{{ $id }}" wire:key="city-{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div wire:loading wire:target="cityId" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="animate-spin h-4 w-4 sm:h-5 sm:w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                <div wire:loading wire:target="provinceId" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg">
                                    <svg class="animate-spin h-4 w-4 sm:h-5 sm:w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('cityId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Kecamatan *</label>
                            <div class="relative">
                                <select wire:model.live="districtId" 
                                        class="w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 @error('districtId') border-red-500 @enderror"
                                        {{ empty($districts) ? 'disabled' : '' }}>
                                    <option value="">Pilih Kecamatan</option>
                                    @foreach($districts as $id => $name)
                                    <option value="{{ $id }}" wire:key="district-{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div wire:loading wire:target="districtId" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="animate-spin h-4 w-4 sm:h-5 sm:w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                <div wire:loading wire:target="cityId" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg">
                                    <svg class="animate-spin h-4 w-4 sm:h-5 sm:w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('districtId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Kelurahan *</label>
                            <div class="relative">
                                <select wire:model.live="subdistrictId" 
                                        class="w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 @error('subdistrictId') border-red-500 @enderror"
                                        {{ empty($subdistricts) ? 'disabled' : '' }}>
                                    <option value="">Pilih Kelurahan</option>
                                    @foreach($subdistricts as $id => $name)
                                    <option value="{{ $id }}" wire:key="subdistrict-{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div wire:loading wire:target="subdistrictId" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="animate-spin h-4 w-4 sm:h-5 sm:w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                <div wire:loading wire:target="districtId" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg">
                                    <svg class="animate-spin h-4 w-4 sm:h-5 sm:w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('subdistrictId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Kode Pos *</label>
                            <input type="text" wire:model="postalCode" 
                                   class="w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 @error('postalCode') border-red-500 @enderror">
                            @error('postalCode') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Shipping Method -->
                <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
                    <h2 class="text-base sm:text-xl font-bold text-gray-900 mb-3 sm:mb-4 flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0 a2 2 0 114 0"></path>
                        </svg>
                        Metode Pengiriman
                    </h2>
                    
                    <!-- Selected Branch Info -->
                    @if($this->selectedBranch && count($branches) > 1)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                        <div class="flex items-start">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 mr-2 sm:mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm sm:text-base font-semibold text-blue-900">Pengiriman dari: {{ $this->selectedBranch->name }}</p>
                                <p class="text-xs sm:text-sm text-blue-700 mt-1">{{ $this->selectedBranch->city_name }}</p>
                                <button wire:click="$set('showBranchModal', true)" class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 mt-2 underline flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Ubah cabang
                                </button>
                            </div>
                            @if($subdistrictId)
                            <div wire:loading wire:target="selectBranch" class="ml-2 sm:ml-4">
                                <svg class="animate-spin h-4 w-4 sm:h-5 sm:w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    @if(empty($districtId))
                    <!-- Message when district not selected -->
                    <div class="text-center py-6 sm:py-8">
                        <div class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400 mb-2 sm:mb-3 flex items-center justify-center">
                            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="mt-1 text-xs sm:text-sm text-gray-500">Lengkapi alamat pengiriman untuk melihat opsi pengiriman</p>
                    </div>
                    @else
                    <!-- Loading indicator for shipping calculation -->
                    <div wire:loading wire:target="districtId" class="flex flex-col items-center justify-center py-6 sm:py-8">
                        <svg class="animate-spin h-8 w-8 sm:h-10 sm:w-10 text-green-600 mb-2 sm:mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-sm sm:text-base text-gray-600">Menghitung ongkos kirim...</p>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">Mohon tunggu sebentar</p>
                    </div>
                    
                    <!-- Shipping options -->
                    <div wire:loading.remove wire:target="districtId">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3">
                            @forelse($shippingMethods as $method)
                            <label class="flex flex-col p-2.5 sm:p-3 border-2 rounded-lg cursor-pointer transition-all {{ $shippingMethod === $method['id'] ? 'border-green-600 bg-green-50' : 'border-gray-200 hover:border-green-300' }}" wire:key="shipping-{{ $method['id'] }}">
                                <div class="flex items-start mb-1.5 sm:mb-2">
                                    <input type="radio" wire:model.live="shippingMethod" value="{{ $method['id'] }}" 
                                           class="mt-0.5 text-green-600 focus:ring-green-500 flex-shrink-0">
                                    <div class="ml-2 flex-1 min-w-0">
                                        <p class="text-xs sm:text-sm font-semibold text-gray-900 leading-tight">{{ $method['name'] }}</p>
                                        <p class="text-xs text-gray-600 mt-0.5 line-clamp-2">{{ $method['description'] }}</p>
                                    </div>
                                </div>
                                <div class="pl-5 mt-1 flex items-center justify-between">
                                    <p class="text-xs text-gray-500">
                                        @php
                                            // Remove 'day' or 'days' from estimation to avoid duplication
                                            $estimation = preg_replace('/\s*(day|days)\s*/i', '', $method['estimatedDays']);
                                        @endphp
                                        Estimasi: {{ $estimation }} hari
                                    </p>
                                    <p class="text-xs sm:text-sm font-bold text-green-600">{{ format_rupiah($method['cost']) }}</p>
                                </div>
                            </label>
                            @empty
                            <div class="col-span-full text-center py-6 sm:py-8">
                                <div class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400 mb-2 sm:mb-3 flex items-center justify-center">
                                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                </div>
                                <h3 class="mt-2 text-xs sm:text-sm font-medium text-gray-900">Metode pengiriman tidak tersedia</h3>
                                <p class="mt-1 text-xs sm:text-sm text-gray-500">Silakan lengkapi alamat pengiriman untuk melihat opsi pengiriman.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    @error('shippingMethod') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
                    <h2 class="text-base sm:text-xl font-bold text-gray-900 mb-3 sm:mb-4 flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Metode Pembayaran
                    </h2>
                    
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3">
                        @foreach($this->paymentMethods as $method)
                        <label class="flex flex-col p-2.5 sm:p-3 border-2 rounded-lg cursor-pointer transition-all {{ $paymentMethod === $method['id'] ? 'border-green-600 bg-green-50' : 'border-gray-200 hover:border-green-300' }}" wire:key="payment-{{ $method['id'] }}">
                            <div class="flex items-start gap-2">
                                <input type="radio" wire:model.live="paymentMethod" value="{{ $method['id'] }}" 
                                       class="mt-0.5 text-green-600 focus:ring-green-500 flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs sm:text-sm font-semibold text-gray-900 leading-tight">{{ $method['name'] }}</p>
                                    <p class="text-xs text-gray-600 mt-0.5 leading-tight">{{ $method['description'] }}</p>
                                    @if($method['id'] === 'cod')
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-1.5 py-0.5 rounded mt-1">POPULER</span>
                                    @endif
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('paymentMethod') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 sticky top-20 sm:top-24">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Ringkasan Pesanan</h3>
                    
                    <!-- Cart Items -->
                    <div class="space-y-2 sm:space-y-3 mb-3 sm:mb-4 max-h-48 sm:max-h-60 overflow-y-auto">
                        @foreach($this->cartItems as $item)
                        <div class="flex items-start space-x-2 sm:space-x-3 pb-2 sm:pb-3 border-b border-gray-100" wire:key="cart-item-{{ $item->id }}">
                            <img src="{{ image_url($item->product->main_image) }}" 
                                 alt="{{ $item->product->name }}" 
                                 class="w-12 h-12 sm:w-16 sm:h-16 object-cover rounded-lg flex-shrink-0"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27100%27 height=%27100%27%3E%3Crect width=%27100%27 height=%27100%27 fill=%27%23f3f4f6%27/%3E%3C/svg%3E'">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $item->product->name }}</p>
                                @if($item->sku->attributes)
                                <p class="text-xs text-gray-500">
                                    @foreach($item->sku->attributes as $key => $value)
                                        {{ $value }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </p>
                                @endif
                                <p class="text-xs text-gray-600 mt-0.5 sm:mt-1">{{ $item->quantity }} x {{ format_rupiah($item->price) }}</p>
                            </div>
                            <p class="text-xs sm:text-sm font-semibold text-gray-900">{{ format_rupiah($item->price * $item->quantity) }}</p>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Price Breakdown -->
                    <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6 pt-3 sm:pt-4 border-t border-gray-200">
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600">Subtotal ({{ $this->cartItems->count() }} item)</span>
                            <span class="font-medium">{{ format_rupiah($this->subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span class="font-medium">
                                @if($shippingCost > 0 || ($shippingCost == 0 && $this->selectedShippingOriginalCost > 0))
                                    @if($shippingCost < $this->selectedShippingOriginalCost)
                                        <span class="text-gray-400 line-through mr-1 text-xs">{{ format_rupiah($this->selectedShippingOriginalCost) }}</span>
                                        <span class="text-green-600">{{ format_rupiah($shippingCost) }}</span>
                                    @else
                                        {{ format_rupiah($shippingCost) }}
                                    @endif
                                @else
                                    <span class="text-gray-500 italic text-xs">Menunggu data</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600">Biaya Layanan</span>
                            <span class="font-medium">{{ format_rupiah($serviceFee) }}</span>
                        </div>
                        @if($discount > 0)
                        <div class="flex justify-between text-xs sm:text-sm text-green-600 font-medium bg-green-50 p-2 rounded border border-green-100 mt-2">
                            <div class="flex items-center">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span>Voucher ({{ strtoupper($voucherCode) }})</span>
                            </div>
                            <span>-{{ format_rupiah($discount) }}</span>
                        </div>
                        @endif

                    </div>
                    
                    <!-- Total -->
                    <div class="border-t border-gray-200 pt-3 sm:pt-4 mb-4 sm:mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-sm sm:text-lg font-semibold text-gray-900">Total: </span>
                            <span class="text-lg sm:text-2xl font-bold text-green-600">{{ format_rupiah($this->total) }}</span>
                        </div>
                    </div>
                    
                    <!-- Place Order Button -->
                    <button wire:click="placeOrder" 
                            wire:loading.attr="disabled"
                            class="w-full bg-green-600 text-white py-2.5 sm:py-3 rounded-lg text-sm sm:text-base font-semibold hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed mb-2 sm:mb-3">
                        <span wire:loading.remove wire:target="placeOrder">Buat Pesanan</span>
                        <span wire:loading wire:target="placeOrder">Memproses...</span>
                    </button>
                    
                    <a href="/cart" class="block w-full text-center border border-gray-300 text-gray-700 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                        Kembali ke Keranjang
                    </a>
                    
                    <!-- Security Badge -->
                    <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-center space-x-2 text-xs text-gray-500">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Transaksi aman & terenkripsi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <x-footer />

    <script>
        document.addEventListener('livewire:init', function() {
            Livewire.on('validation-failed', () => {
                setTimeout(() => {
                    const firstError = document.querySelector('.border-red-500, .text-red-500');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        // If it's an input/select/textarea, focus it
                        if (['INPUT', 'SELECT', 'TEXTAREA'].includes(firstError.tagName)) {
                            firstError.focus();
                        }
                    }
                }, 100);
            });
        });
    </script>
</div>
