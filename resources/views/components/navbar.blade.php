<nav class="glass-nav fixed top-0 w-full z-50 border-b border-gray-200" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-14 sm:h-16">
            <!-- Logo -->
            <div class="flex items-center flex-shrink-0">
                <a href="/" class="flex flex-col items-end leading-none group">
                    <span class="text-xl sm:text-3xl font-black text-green-600 tracking-tighter hover:text-green-700 transition-colors">buatserba</span>
                    <span class="text-[0.6rem] sm:text-xs font-bold text-green-600 -mt-1 sm:-mt-1.5 mr-0.5 group-hover:text-green-700 transition-colors">.com</span>
                </a>
            </div>
            
            <!-- Search Bar - Mobile & Desktop -->
            <div class="flex-1 max-w-xs sm:max-w-md lg:max-w-2xl mx-2 sm:mx-4 lg:mx-8">
                <form action="/catalog" method="GET">
                    <div class="relative">
                        <input type="text" name="search" 
                               placeholder="Cari produk..." 
                               class="w-full px-3 py-1.5 pl-8 pr-3 text-xs sm:text-sm text-gray-700 bg-white border border-gray-300 rounded-full focus:outline-none focus:border-green-500">
                        <svg class="absolute left-2.5 top-1.5 sm:top-2 h-4 w-4 sm:h-5 sm:w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>
            </div>
            
            <!-- Desktop Navigation Links - Hidden on Mobile -->
            <div class="hidden lg:flex items-center space-x-6">
                <a href="/" class="{{ request()->is('/') ? 'text-green-600' : 'text-gray-700 hover:text-green-600' }} font-medium text-sm">Beranda</a>
                <a href="/catalog" class="{{ request()->is('catalog*') ? 'text-green-600' : 'text-gray-700 hover:text-green-600' }} font-medium text-sm">Katalog</a>
                <a href="/cart" class="{{ request()->is('cart*') ? 'text-green-600' : 'text-gray-700 hover:text-green-600' }} font-medium text-sm relative">
                    Keranjang
                    @if(isset($cartCount) && $cartCount > 0)
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                        {{ $cartCount }}
                    </span>
                    @else
                    <span class="cart-count absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center" style="display: none;">0</span>
                    @endif
                </a>
                
                @auth
                    <!-- Authenticated User -->
                    <a href="{{ route('dashboard') }}" class="{{ request()->is('user*') ? 'text-green-600' : 'text-gray-700 hover:text-green-600' }} font-medium text-sm">
                        Dashboard
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-red-600 font-medium text-sm transition-colors">
                            Logout
                        </button>
                    </form>
                @else
                    <!-- Guest User -->
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-green-600 font-medium text-sm">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                        Daftar
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden flex items-center justify-center w-8 h-8 text-gray-700 hover:text-green-600 focus:outline-none">
                <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <svg x-show="mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Dropdown -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         @click.away="mobileMenuOpen = false"
         class="lg:hidden border-t border-gray-200 bg-white shadow-lg"
         style="display: none;">
        <div class="px-4 py-3 space-y-2">
            <a href="/" class="{{ request()->is('/') ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:bg-gray-50' }} block px-3 py-2 rounded-md text-sm font-medium">
                Beranda
            </a>
            <a href="/catalog" class="{{ request()->is('catalog*') ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:bg-gray-50' }} block px-3 py-2 rounded-md text-sm font-medium">
                Katalog
            </a>
            <a href="/cart" class="{{ request()->is('cart*') ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:bg-gray-50' }} block px-3 py-2 rounded-md text-sm font-medium relative">
                <span>Keranjang</span>
                @if(isset($cartCount) && $cartCount > 0)
                <span class="ml-2 inline-flex items-center justify-center bg-red-500 text-white text-xs rounded-full w-5 h-5">
                    {{ $cartCount }}
                </span>
                @else
                <span class="cart-count-mobile ml-2 inline-flex items-center justify-center bg-red-500 text-white text-xs rounded-full w-5 h-5" style="display: none;">0</span>
                @endif
            </a>
            
            @auth
                <a href="{{ route('dashboard') }}" class="{{ request()->is('user*') ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:bg-gray-50' }} block px-3 py-2 rounded-md text-sm font-medium">
                    Dashboard
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left text-red-600 hover:bg-red-50 block px-3 py-2 rounded-md text-sm font-medium">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-gray-700 hover:bg-gray-50 block px-3 py-2 rounded-md text-sm font-medium">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="bg-green-600 text-white hover:bg-green-700 block px-3 py-2 rounded-md text-sm font-medium text-center">
                    Daftar
                </a>
            @endauth
        </div>
    </div>
</nav>

<style>
    .glass-nav { 
        backdrop-filter: blur(10px); 
        background: rgba(255, 255, 255, 0.9); 
    }
</style>
