<div>
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
        Daftar Akun {{ env('APP_NAME') }}
    </h2>

    <form wire:submit.prevent="register" class="space-y-4">
        <div>
            <div class="floating-label-group">
                <input wire:model="name" 
                       class="text-gray-900" 
                       id="name" 
                       type="text" 
                       required />
                <label for="name">Nama Lengkap</label>
            </div>
            @error('name')
                <p class="text-xs text-red-600 mt-1 ml-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="floating-label-group">
                <input wire:model="email" 
                       class="text-gray-900" 
                       id="email" 
                       type="email" 
                       required />
                <label for="email">Email</label>
            </div>
            @error('email')
                <p class="text-xs text-red-600 mt-1 ml-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="floating-label-group">
                <input wire:model="phone" 
                       class="text-gray-900" 
                       id="phone" 
                       type="text" 
                       placeholder="" />
                <label for="phone">Nomor HP (Opsional)</label>
            </div>
            @error('phone')
                <p class="text-xs text-red-600 mt-1 ml-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="floating-label-group">
                <input wire:model="password" 
                       class="text-gray-900" 
                       id="password" 
                       type="password" 
                       required />
                <label for="password">Password</label>
            </div>
            @error('password')
                <p class="text-xs text-red-600 mt-1 ml-1">{{ $message }}</p>
            @else
                <p class="text-xs text-gray-500 mt-1 ml-1">
                    Minimal 8 karakter
                </p>
            @enderror
        </div>

        <div>
            <div class="floating-label-group">
                <input wire:model="password_confirmation" 
                       class="text-gray-900" 
                       id="password_confirmation" 
                       type="password" 
                       required />
                <label for="password_confirmation">Konfirmasi Password</label>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <a class="text-sm font-semibold text-green-600 hover:text-green-700 transition-colors" 
               href="{{ route('login') }}">
                Sudah Punya Akun? Login Di Sini
            </a>
        </div>

        <button class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600" 
                type="submit">
            Daftar
        </button>
    </form>
</div>
