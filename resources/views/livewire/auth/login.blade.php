<div>
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
        Masuk ke {{ env('APP_NAME') }}
    </h2>

    <form wire:submit.prevent="login" class="space-y-6">
        <div>
            <div class="floating-label-group">
                <input wire:model="email" 
                       class="text-gray-900" 
                       id="email" 
                       type="email" 
                       required 
                       autofocus />
                <label for="email">Email</label>
            </div>
            @error('email')
                <p class="text-xs text-red-600 mt-1 ml-1">{{ $message }}</p>
            @else
                <p class="text-xs text-gray-500 mt-1 ml-1">
                    Contoh: user@example.com
                </p>
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
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input wire:model="remember"
                       id="remember" 
                       type="checkbox" 
                       class="h-4 w-4 text-green-600 focus:ring-green-600 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-700">
                    Ingat saya
                </label>
            </div>

        </div>

        <button class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600" 
                type="submit"
                wire:loading.attr="disabled">
            <span wire:loading.remove>Masuk</span>
            <span wire:loading>
                <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
        </button>

        <div class="text-center pt-4">
            <p class="text-sm text-gray-600">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-semibold text-green-600 hover:text-green-700 transition-colors">
                    Daftar Sekarang
                </a>
            </p>
        </div>
    </form>
</div>
