<footer class="bg-gray-900 text-white py-8 sm:py-10 md:py-12 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8">
            <!-- Brand -->
            <div>
                <a href="/" class="flex items-start leading-none group font-['Poppins']">
                    <span class="text-xl sm:text-3xl font-bold text-green-600 tracking-tighter hover:text-green-700 transition-colors">buatserba</span>
                    <span class="text-[0.6rem] sm:text-xs text-green-600 ml-0.5 group-hover:text-green-700 transition-colors">®</span>
                </a>
                <p class="text-xs sm:text-sm text-gray-400 leading-relaxed">Platform belanja online terpercaya dengan produk berkualitas dan harga terbaik. Kami hadir sebagai solusi belanja one-stop shopping untuk memenuhi semua kebutuhan Anda.</p>
            </div>
            
            <!-- Kategori -->
            @if(isset($categories) && $categories)
            <div>
                <h5 class="text-sm sm:text-base font-semibold mb-3 sm:mb-4">Kategori</h5>
                <ul class="space-y-1.5 sm:space-y-2 text-xs sm:text-sm text-gray-400">
                    @foreach($categories->take(4) as $category)
                    <li><a href="/{{ $category->slug }}" class="hover:text-white transition-colors">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <!-- Layanan -->
            <div>
                <h5 class="text-sm sm:text-base font-semibold mb-3 sm:mb-4">Layanan</h5>
                <ul class="space-y-1.5 sm:space-y-2 text-xs sm:text-sm text-gray-400">
                    <li><a href="/about" class="hover:text-white transition-colors">Tentang Kami</a></li>
                    <li><a href="/faq" class="hover:text-white transition-colors">FAQ</a></li>
                    <li><a href="/terms-conditions" class="hover:text-white transition-colors">Syarat & Ketentuan</a></li>
                    <li><a href="/privacy-policy" class="hover:text-white transition-colors">Kebijakan Privasi</a></li>
                    <li><a href="/return-refund-policy" class="hover:text-white transition-colors">Kebijakan Retur & Refund</a></li>
                </ul>
            </div>
            
            <!-- Hubungi Kami -->
            <div>
                <h5 class="text-sm sm:text-base font-semibold mb-3 sm:mb-4">Hubungi Kami</h5>
                <ul class="space-y-1.5 sm:space-y-2 text-xs sm:text-sm text-gray-400">
                    <li>Alamat: {{ global_config('address') }}</li>
                    <li>Email: {{ global_config('email') }}</li>
                    <li>Telepon: {{ global_config('phone') }}</li>
                    <li>WhatsApp: {{ global_config('whatsapp') }}</li>
                </ul>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="border-t border-gray-800 mt-6 sm:mt-8 pt-6 sm:pt-8 text-center">
            <p class="text-xs sm:text-sm text-gray-400">&copy; {{ date('Y') }} {{ global_config('company_name') }}. All Rights Reserved.</p>
        </div>
    </div>
</footer>
