<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard - BuatSerba' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @livewireStyles
</head>
<body style="font-family: 'Inter', sans-serif;">
    <div class="bg-gray-50 min-h-screen">
        <!-- Navigation -->
        <x-navbar />
        
        <!-- Main Content -->
        <div class="pt-20 pb-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Sidebar -->
                    <aside class="w-full lg:w-1/4 flex-shrink-0">
                        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden lg:sticky lg:top-20">
                            <div class="p-4 flex items-center gap-3 border-b border-gray-200">
                                <div class="w-12 h-12 rounded-full bg-green-600 flex items-center justify-center text-white font-bold text-lg">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <h2 class="font-bold text-sm text-gray-900">
                                        {{ auth()->user()->name }}
                                    </h2>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                            <nav class="py-2">
                                <a href="{{ route('dashboard') }}" 
                                   class="flex items-center px-4 py-3 text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'text-green-600 bg-green-50 border-l-4 border-green-600' : 'text-gray-700 hover:bg-gray-50 border-l-4 border-transparent' }}">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Daftar Transaksi
                                </a>
                                <a href="#" 
                                   class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 border-l-4 border-transparent transition-colors">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Profil
                                </a>
                                <div class="border-t border-gray-200 my-2"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 border-l-4 border-transparent hover:border-red-600 transition-colors">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </nav>
                        </div>
                    </aside>

                    <!-- Main Content Area -->
                    <div class="w-full lg:w-3/4">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h4 class="text-2xl font-bold text-green-600 mb-4">BuatSerba</h4>
                        <p class="text-gray-400 mb-4">Platform belanja online terpercaya dengan produk berkualitas dan harga terbaik.</p>
                    </div>
                    
                    <div>
                        <h5 class="font-semibold mb-4">Layanan</h5>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white">Bantuan</a></li>
                            <li><a href="#" class="hover:text-white">Kebijakan Pengembalian</a></li>
                            <li><a href="#" class="hover:text-white">Syarat & Ketentuan</a></li>
                            <li><a href="#" class="hover:text-white">Kebijakan Privasi</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h5 class="font-semibold mb-4">Hubungi Kami</h5>
                        <ul class="space-y-2 text-gray-400">
                            <li>Email: cs@buatserba.com</li>
                            <li>Telepon: 0800-123-4567</li>
                            <li>Jam Operasional: 24/7</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h5 class="font-semibold mb-4">Ikuti Kami</h5>
                        <div class="flex space-x-4 text-gray-400">
                            <a href="#" class="hover:text-white">Facebook</a>
                            <a href="#" class="hover:text-white">Instagram</a>
                            <a href="#" class="hover:text-white">Twitter</a>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} BuatSerba. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Notification -->
        <div x-data="{ show: false, message: '', type: 'success' }"
             x-on:notify.window="message = $event.detail.message; type = $event.detail.type; show = true; setTimeout(() => show = false, 5000)"
             x-show="show" x-transition.opacity
             class="fixed top-20 right-4 z-50 max-w-md">
            <div :class="{
                'bg-green-100 border-green-500 text-green-900': type === 'success',
                'bg-red-100 border-red-500 text-red-900': type === 'error'
            }" class="border-l-4 p-4 rounded shadow-lg">
                <p class="font-medium" x-text="message"></p>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
