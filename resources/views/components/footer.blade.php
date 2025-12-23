<footer class="bg-gray-900 text-white py-8 sm:py-10 md:py-12 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8">
            <!-- Brand -->
            <div>
                <h4 class="text-lg sm:text-xl font-bold text-green-600 mb-3 sm:mb-4">BuatSerba</h4>
                <p class="text-xs sm:text-sm text-gray-400 leading-relaxed">Platform belanja online terpercaya dengan produk berkualitas dan harga terbaik.</p>
            </div>
            
            <!-- Kategori - Only visible on pages that have categories -->
            @if(isset($categories) && $categories)
            <div>
                <h5 class="text-sm sm:text-base font-semibold mb-3 sm:mb-4">Kategori</h5>
                <ul class="space-y-1.5 sm:space-y-2 text-xs sm:text-sm text-gray-400">
                    @foreach($categories->take(4) as $category)
                    <li><a href="/catalog?selectedCategories[]={{ $category->id }}" class="hover:text-white transition-colors">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <!-- Layanan -->
            <div>
                <h5 class="text-sm sm:text-base font-semibold mb-3 sm:mb-4">Layanan</h5>
                <ul class="space-y-1.5 sm:space-y-2 text-xs sm:text-sm text-gray-400">
                    <li><a href="#" class="hover:text-white transition-colors">Bantuan</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Kebijakan Pengembalian</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a></li>
                </ul>
            </div>
            
            <!-- Hubungi Kami -->
            <div>
                <h5 class="text-sm sm:text-base font-semibold mb-3 sm:mb-4">Hubungi Kami</h5>
                <ul class="space-y-1.5 sm:space-y-2 text-xs sm:text-sm text-gray-400">
                    <li>Email: support@buatserba.com</li>
                    <li>Telepon: 0800-123-4567</li>
                    <li>Jam Operasional: 24/7</li>
                </ul>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="border-t border-gray-800 mt-6 sm:mt-8 pt-6 sm:pt-8 text-center">
            <p class="text-xs sm:text-sm text-gray-400">&copy; {{ date('Y') }} BuatSerba. All rights reserved.</p>
        </div>
    </div>
</footer>
