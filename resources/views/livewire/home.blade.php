<div class="bg-gray-50">
    <!-- Navigation -->
    <x-navbar />

    <!-- Hero Slider -->
    <div class="pt-16">
        <div class="relative overflow-hidden bg-gradient-to-r from-green-600 to-green-700" style="height: 400px;">
            <div class="hero-slider">
                <!-- Slide 1 -->
                <div class="hero-slide active absolute inset-0 transition-opacity duration-700">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center">
                        <div class="text-white max-w-2xl">
                            <h1 class="text-5xl font-bold mb-4">Selamat Datang di BuatSerba</h1>
                            <p class="text-xl mb-6">Platform belanja online terpercaya dengan produk berkualitas dan harga terbaik</p>
                            <a href="/catalog" class="inline-block bg-white text-green-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                                Mulai Belanja
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="hero-slide absolute inset-0 transition-opacity duration-700 opacity-0">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center">
                        <div class="text-white max-w-2xl">
                            <h1 class="text-5xl font-bold mb-4">Produk Terlengkap</h1>
                            <p class="text-xl mb-6">Ribuan produk pilihan dari berbagai kategori untuk memenuhi kebutuhan Anda</p>
                            <a href="/catalog" class="inline-block bg-white text-green-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                                Lihat Katalog
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="hero-slide absolute inset-0 transition-opacity duration-700 opacity-0">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center">
                        <div class="text-white max-w-2xl">
                            <h1 class="text-5xl font-bold mb-4">Harga Terbaik</h1>
                            <p class="text-xl mb-6">Dapatkan penawaran spesial dan harga grosir untuk pembelian dalam jumlah banyak</p>
                            <a href="/catalog" class="inline-block bg-white text-green-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                                Belanja Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slider Navigation -->
            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-3">
                <button class="slider-dot w-3 h-3 rounded-full bg-white opacity-50 hover:opacity-100 transition-opacity active" data-slide="0"></button>
                <button class="slider-dot w-3 h-3 rounded-full bg-white opacity-50 hover:opacity-100 transition-opacity" data-slide="1"></button>
                <button class="slider-dot w-3 h-3 rounded-full bg-white opacity-50 hover:opacity-100 transition-opacity" data-slide="2"></button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Best Selling Products Section -->
        <section class="mb-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Produk Terlaris</h2>
                <a href="/catalog?sortBy=popularity" class="text-green-600 hover:text-green-700 font-medium flex items-center">
                    Lihat Semua
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                @forelse($bestSellingProducts as $product)
                <div class="bg-white rounded-lg shadow-md card-hover overflow-hidden">
                    <a href="/product/{{ $product->slug }}" class="block">
                        <div class="relative pb-[100%] bg-gray-100">
                            <img src="{{ product_image($product) }}" 
                                 alt="{{ $product->name }}" 
                                 class="absolute inset-0 w-full h-full object-cover"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27400%27 height=%27400%27%3E%3Crect width=%27400%27 height=%27400%27 fill=%27%23f3f4f6%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 dominant-baseline=%27middle%27 text-anchor=%27middle%27 font-family=%27monospace%27 font-size=%2726px%27 fill=%27%239ca3af%27%3ENo Image%3C/text%3E%3C/svg%3E'">
                            @if($product->is_featured)
                            <span class="absolute top-2 right-2 bg-green-600 text-white text-xs px-2 py-1 rounded-full">
                                Featured
                            </span>
                            @endif
                        </div>
                        <div class="p-3">
                            <p class="text-xs text-gray-500 mb-1">{{ $product->category->name ?? 'Uncategorized' }}</p>
                            <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                            @php
                                $sku = $product->skus->first();
                            @endphp
                            @if($sku)
                            <div class="mt-2">
                                <div class="flex items-baseline space-x-1">
                                    <span class="text-base font-bold text-green-600">
                                        {{ format_rupiah($sku->selling_price) }}
                                    </span>
                                </div>
                                @if($sku->base_price > $sku->selling_price)
                                <div class="flex items-center space-x-1 mt-1">
                                    <span class="text-xs text-gray-500 line-through">
                                        {{ format_rupiah($sku->base_price) }}
                                    </span>
                                    @php
                                        $discountPercent = discount_percentage($sku->base_price, $sku->selling_price);
                                    @endphp
                                    @if($discountPercent > 0)
                                    <span class="text-xs bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full">
                                        -{{ $discountPercent }}%
                                    </span>
                                    @endif
                                </div>
                                @endif
                            </div>
                            @endif
                            <div class="mt-2 flex items-center text-yellow-400 text-xs">
                                @for($i = 0; $i < 5; $i++)
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                        </div>
                    </a>
                </div>
                @empty
                <div class="col-span-full text-center py-12 text-gray-500">
                    Belum ada produk tersedia
                </div>
                @endforelse
            </div>
        </section>

        <!-- About Us Section -->
        <section class="mb-16">
            <div class="bg-white rounded-lg shadow-lg p-8 md:p-12">
                <div class="max-w-4xl mx-auto text-center">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Tentang BuatSerba</h2>
                    <div class="text-gray-700 leading-relaxed space-y-4">
                        <p class="text-lg">
                            BuatSerba adalah platform e-commerce terpercaya yang menyediakan berbagai macam produk berkualitas dengan harga terbaik. 
                            Kami berkomitmen untuk memberikan pengalaman belanja online yang mudah, aman, dan menyenangkan bagi seluruh pelanggan kami.
                        </p>
                        <p class="text-lg">
                            Dengan koleksi produk yang lengkap dari berbagai kategori, mulai dari elektronik, fashion, peralatan rumah tangga, 
                            hingga kebutuhan sehari-hari, BuatSerba hadir sebagai solusi belanja one-stop shopping untuk memenuhi semua kebutuhan Anda.
                        </p>
                        <p class="text-lg">
                            Kami menjamin keaslian produk, harga kompetitif, pengiriman cepat, dan layanan pelanggan yang responsif. 
                            Kepuasan pelanggan adalah prioritas utama kami, dan kami terus berinovasi untuk memberikan layanan terbaik.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                            <div class="p-6 bg-green-50 rounded-lg">
                                <div class="text-green-600 mb-3">
                                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 mb-2">Produk Berkualitas</h3>
                                <p class="text-sm text-gray-600">100% produk original dan terjamin kualitasnya</p>
                            </div>
                            <div class="p-6 bg-green-50 rounded-lg">
                                <div class="text-green-600 mb-3">
                                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 mb-2">Harga Terbaik</h3>
                                <p class="text-sm text-gray-600">Harga kompetitif dengan penawaran spesial</p>
                            </div>
                            <div class="p-6 bg-green-50 rounded-lg">
                                <div class="text-green-600 mb-3">
                                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 mb-2">Pengiriman Cepat</h3>
                                <p class="text-sm text-gray-600">Pengiriman ke seluruh Indonesia dengan cepat</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-2xl font-bold text-green-600 mb-4">BuatSerba</h4>
                    <p class="text-gray-400 mb-4">Platform belanja online terpercaya dengan produk berkualitas dan harga terbaik.</p>
                </div>
                
                <div>
                    <h5 class="font-semibold mb-4">Kategori</h5>
                    <ul class="space-y-2 text-gray-400">
                        @foreach($categories->take(4) as $category)
                        <li><a href="/catalog?selectedCategories[]={{ $category->id }}" class="hover:text-white">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
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
                        <li>Email: support@buatserba.com</li>
                        <li>Telepon: 0800-123-4567</li>
                        <li>Jam Operasional: 24/7</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} BuatSerba. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <style>
        .card-hover { 
            transition: all 0.3s ease; 
        }
        .card-hover:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
        }
        .line-clamp-2 { 
            display: -webkit-box; 
            -webkit-line-clamp: 2; 
            -webkit-box-orient: vertical; 
            overflow: hidden; 
        }
        .hero-slide {
            transition: opacity 0.7s ease-in-out;
        }
        .slider-dot.active {
            opacity: 1;
        }
    </style>

    <script>
        // Hero Slider
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.slider-dot');
        
        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle('opacity-0', i !== index);
                slide.classList.toggle('active', i === index);
            });
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
            currentSlide = index;
        }
        
        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }
        
        // Auto advance slides
        setInterval(nextSlide, 5000);
        
        // Dot navigation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => showSlide(index));
        });
    </script>
</div>
