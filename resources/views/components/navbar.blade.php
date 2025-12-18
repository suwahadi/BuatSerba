<nav class="glass-nav fixed top-0 w-full z-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="/" class="text-2xl font-bold text-green-600">BuatSerba</a>
            </div>
            
            <!-- Search Bar -->
            <div class="flex-1 max-w-2xl mx-8">
                <form action="/catalog" method="GET">
                    <div class="relative">
                        <input type="text" name="search" 
                               placeholder="Cari produk, brand, atau kategori..." 
                               class="w-full px-4 py-2 pl-10 pr-4 text-gray-700 bg-white border border-gray-300 rounded-full focus:outline-none focus:border-green-500">
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>
            </div>
            
            <!-- Navigation Links -->
            <div class="flex items-center space-x-6">
                <a href="/" class="{{ request()->is('/') ? 'text-green-600' : 'text-gray-700 hover:text-green-600' }} font-medium">Beranda</a>
                <a href="/catalog" class="{{ request()->is('catalog*') ? 'text-green-600' : 'text-gray-700 hover:text-green-600' }} font-medium">Katalog</a>
                <a href="/cart" class="{{ request()->is('cart*') ? 'text-green-600' : 'text-gray-700 hover:text-green-600' }} font-medium relative">
                    Keranjang
                    @if(isset($cartCount) && $cartCount > 0)
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                        {{ $cartCount }}
                    </span>
                    @else
                    <span class="cart-count absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center" style="display: none;">0</span>
                    @endif
                </a>
                <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    Masuk
                </button>
            </div>
        </div>
    </div>
</nav>

<style>
    .glass-nav { 
        backdrop-filter: blur(10px); 
        background: rgba(255, 255, 255, 0.9); 
    }
</style>
