<div class="bg-gray-50">
    <!-- Navigation -->
    <x-navbar />

    <!-- Hero Slider -->
    <div class="pt-16">
        <div class="relative overflow-hidden bg-gray-200">
            <div class="hero-slider">
                <!-- Slide 1 -->
                <div class="hero-slide active transition-opacity duration-700">
                    <div class="relative w-full aspect-[16/10.67] md:aspect-[1600/600]">
                        <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?q=80&w=1600&h=600&fit=crop" 
                             alt="Banner 1" 
                             class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="hero-slide absolute inset-0 transition-opacity duration-700 opacity-0">
                    <div class="relative w-full aspect-[16/10.67] md:aspect-[1600/600]">
                        <img src="https://images.unsplash.com/photo-1485125639709-a60c3a500bf1?q=80&w=1600&h=600&fit=crop" 
                             alt="Banner 2" 
                             class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="hero-slide absolute inset-0 transition-opacity duration-700 opacity-0">
                    <div class="relative w-full aspect-[16/10.67] md:aspect-[1600/600]">
                        <img src="https://images.unsplash.com/photo-1573879500655-98f2012dd1db?q=80&w=1600&h=600&fit=crop" 
                             alt="Banner 3" 
                             class="w-full h-full object-cover">
                    </div>
                </div>
            </div>

            <!-- Slider Navigation -->
            <div class="absolute bottom-4 sm:bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-2 sm:space-x-3 z-10">
                <button class="slider-dot w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-white opacity-50 hover:opacity-100 transition-opacity active" data-slide="0"></button>
                <button class="slider-dot w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-white opacity-50 hover:opacity-100 transition-opacity" data-slide="1"></button>
                <button class="slider-dot w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-white opacity-50 hover:opacity-100 transition-opacity" data-slide="2"></button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Best Selling Products Section -->
        <section class="mb-12 sm:mb-16">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Produk Terlaris</h2>
                <a href="/catalog?sortBy=popularity" class="text-sm sm:text-base text-green-600 hover:text-green-700 font-medium flex items-center">
                    Lihat Semua
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <section class="mb-12 sm:mb-16">
            <div class="bg-white rounded-lg shadow-lg p-6 sm:p-8 md:p-10">
                <div class="max-w-4xl mx-auto text-center">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">Tentang BuatSerba</h2>
                    <div class="text-gray-700 leading-relaxed space-y-3 sm:space-y-4">
                        <p class="text-sm sm:text-base">
                            BuatSerba adalah platform e-commerce terpercaya yang menyediakan berbagai macam produk berkualitas dengan harga terbaik. 
                            Kami berkomitmen untuk memberikan pengalaman belanja online yang mudah, aman, dan menyenangkan bagi seluruh pelanggan kami.
                        </p>
                        <p class="text-sm sm:text-base">
                            Dengan koleksi produk yang lengkap dari berbagai kategori, mulai dari elektronik, fashion, peralatan rumah tangga, 
                            hingga kebutuhan sehari-hari, BuatSerba hadir sebagai solusi belanja one-stop shopping untuk memenuhi semua kebutuhan Anda.
                        </p>
                        <p class="text-sm sm:text-base">
                            Kami menjamin keaslian produk, harga kompetitif, pengiriman cepat, dan layanan pelanggan yang responsif. 
                            Kepuasan pelanggan adalah prioritas utama kami, dan kami terus berinovasi untuk memberikan layanan terbaik.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mt-6 sm:mt-8">
                            <div class="p-4 sm:p-6 bg-green-50 rounded-lg">
                                <div class="text-green-600 mb-2 sm:mb-3">
                                    <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-1 sm:mb-2">Produk Berkualitas</h3>
                                <p class="text-xs sm:text-sm text-gray-600">100% produk original dan terjamin kualitasnya</p>
                            </div>
                            <div class="p-4 sm:p-6 bg-green-50 rounded-lg">
                                <div class="text-green-600 mb-2 sm:mb-3">
                                    <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-1 sm:mb-2">Harga Terbaik</h3>
                                <p class="text-xs sm:text-sm text-gray-600">Harga kompetitif dengan penawaran spesial</p>
                            </div>
                            <div class="p-4 sm:p-6 bg-green-50 rounded-lg">
                                <div class="text-green-600 mb-2 sm:mb-3">
                                    <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-1 sm:mb-2">Pengiriman Cepat</h3>
                                <p class="text-xs sm:text-sm text-gray-600">Pengiriman ke seluruh Indonesia dengan cepat</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- Footer -->
    <x-footer :categories="$categories" />

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
