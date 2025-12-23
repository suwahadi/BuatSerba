<div>
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4 sm:p-6">
        <div class="border-b border-gray-200 pb-3 sm:pb-4 mb-4 sm:mb-6">
            <h2 class="text-base sm:text-lg font-bold text-gray-900">Profil Saya</h2>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Kelola informasi profil Anda</p>
        </div>

        <form wire:submit.prevent="updateProfile" class="space-y-4 sm:space-y-5">
            <!-- Name -->
            <div>
                <label for="name" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       wire:model="name"
                       class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                       placeholder="Masukkan nama lengkap">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       id="email" 
                       wire:model="email"
                       class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                       placeholder="email@example.com">
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                    Nomor Telepon
                </label>
                <input type="tel" 
                       id="phone" 
                       wire:model="phone"
                       class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                       placeholder="08xxxxxxxxxx">
                @error('phone')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 pt-4 sm:pt-5">
                <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-3 sm:mb-4">Ubah Password</h3>
                <p class="text-xs text-gray-600 mb-3 sm:mb-4">Kosongkan jika tidak ingin mengubah password</p>
            </div>

            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                    Password Lama
                </label>
                <input type="password" 
                       id="current_password" 
                       wire:model="current_password"
                       class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                       placeholder="Masukkan password lama">
                @error('current_password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label for="new_password" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                    Password Baru
                </label>
                <input type="password" 
                       id="new_password" 
                       wire:model="new_password"
                       class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                       placeholder="Minimal 8 karakter">
                @error('new_password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm New Password -->
            <div>
                <label for="new_password_confirmation" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                    Konfirmasi Password Baru
                </label>
                <input type="password" 
                       id="new_password_confirmation" 
                       wire:model="new_password_confirmation"
                       class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                       placeholder="Ulangi password baru">
                @error('new_password_confirmation')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Requirements -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <p class="text-xs font-medium text-blue-900 mb-1">Persyaratan Password:</p>
                <ul class="text-xs text-blue-800 space-y-0.5 list-disc list-inside">
                    <li>Minimal 8 karakter</li>
                    <li>Password baru harus sama dengan konfirmasi password</li>
                    <li>Masukkan password lama untuk verifikasi</li>
                </ul>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-2 sm:gap-3 pt-2 sm:pt-4">
                <button type="submit"
                        class="px-4 sm:px-6 py-2 sm:py-2.5 bg-green-600 text-white text-xs sm:text-sm font-medium rounded-lg hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Simpan Perubahan
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
