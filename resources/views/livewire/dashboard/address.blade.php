<div>
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4 sm:p-6">
        <div class="border-b border-gray-200 pb-3 sm:pb-4 mb-4 sm:mb-6">
            <h2 class="text-base sm:text-lg font-bold text-gray-900">Alamat Saya</h2>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Kelola alamat pengiriman Anda</p>
        </div>

        <form wire:submit.prevent="saveAddress" class="space-y-4">
            <!-- Province -->
            <div>
                <label for="provinceId" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                    Provinsi <span class="text-red-500">*</span>
                </label>
                <select id="provinceId" 
                        wire:model.live="provinceId"
                        class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                    <option value="">Pilih Provinsi</option>
                    @foreach($provinces as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('provinceId')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- City -->
            <div>
                <label for="cityId" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                    Kota/Kabupaten <span class="text-red-500">*</span>
                </label>
                <select id="cityId" 
                        wire:model.live="cityId"
                        class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                        {{ empty($cities) ? 'disabled' : '' }}>
                    <option value="">Pilih Kota/Kabupaten</option>
                    @foreach($cities as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('cityId')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- District -->
            <div>
                <label for="districtId" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                    Kecamatan <span class="text-red-500">*</span>
                </label>
                <select id="districtId" 
                        wire:model.live="districtId"
                        class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                        {{ empty($districts) ? 'disabled' : '' }}>
                    <option value="">Pilih Kecamatan</option>
                    @foreach($districts as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('districtId')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subdistrict (Optional) -->
            <div>
                <label for="subdistrictId" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                    Kelurahan (Opsional)
                </label>
                <select id="subdistrictId" 
                        wire:model="subdistrictId"
                        class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                        {{ empty($subdistricts) ? 'disabled' : '' }}>
                    <option value="">Pilih Kelurahan</option>
                    @foreach($subdistricts as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Postal Code -->
            <div>
                <label for="postalCode" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                    Kode Pos <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="postalCode" 
                       wire:model="postalCode"
                       maxlength="5"
                       class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                       placeholder="12345">
                @error('postalCode')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Full Address -->
            <div>
                <label for="fullAddress" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                    Alamat Lengkap <span class="text-red-500">*</span>
                </label>
                <textarea id="fullAddress" 
                          wire:model="fullAddress"
                          rows="3"
                          class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition resize-none"
                          placeholder="Jl. Nama Jalan, No. xxx, RT/RW, Detail lainnya..."></textarea>
                @error('fullAddress')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Masukkan detail alamat seperti nama jalan, nomor rumah, RT/RW, patokan, dll.</p>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-2 sm:gap-3 pt-2 sm:pt-4">
                <button type="submit"
                        class="px-4 sm:px-6 py-2 sm:py-2.5 bg-green-600 text-white text-xs sm:text-sm font-medium rounded-lg hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    {{ $hasExistingAddress ? 'Perbarui Alamat' : 'Simpan Alamat' }}
                </button>
                <a href="{{ route('dashboard') }}" 
                   class="px-4 sm:px-6 py-2 sm:py-2.5 border border-gray-300 text-gray-700 text-xs sm:text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@script
<script>
$wire.on('notify-success', (event) => {
    window.dispatchEvent(new CustomEvent('notify', { 
        detail: { message: event.message, type: 'success' } 
    }));
});

$wire.on('notify-error', (event) => {
    window.dispatchEvent(new CustomEvent('notify', { 
        detail: { message: event.message, type: 'error' } 
    }));
});
</script>
@endscript
