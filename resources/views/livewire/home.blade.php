<div class="bg-gray-50">
    <x-navbar />

    <div class="pt-16">
        <div class="relative overflow-hidden bg-gray-200">
            <div class="hero-slider">
                @forelse($banners as $index => $banner)
                    <div class="hero-slide {{ $index === 0 ? 'relative active' : 'absolute inset-0 opacity-0' }} transition-opacity duration-700">
                        <div class="relative w-full aspect-[16/10.67] md:aspect-[1600/600]">
                            @if($banner->url)
                                <a href="{{ $banner->url }}" class="block w-full h-full">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($banner->image) }}" 
                                         alt="{{ $banner->title }}" 
                                         class="w-full h-full object-cover">
                                </a>
                            @else
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($banner->image) }}" 
                                     alt="{{ $banner->title }}" 
                                     class="w-full h-full object-cover">
                            @endif
                        </div>
                    </div>
                @empty
                    <!-- Default Slides if no banner -->
                    <div class="hero-slide relative active transition-opacity duration-700">
                        <div class="relative w-full aspect-[16/10.67] md:aspect-[1600/600]">
                            <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?q=80&w=1600&h=600&fit=crop" 
                                 alt="Default Banner" 
                                 class="w-full h-full object-cover">
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Slider Navigation -->
            @if($banners->count() > 1)
            <div class="absolute bottom-4 sm:bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-2 sm:space-x-3 z-10">
                @foreach($banners as $index => $banner)
                    <button class="slider-dot w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-white opacity-50 hover:opacity-100 transition-opacity {{ $index === 0 ? 'active' : '' }}" 
                            data-slide="{{ $index }}"></button>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Category Carousel Section -->
        <livewire:category-carousel />

        <!-- Latest Products Section -->
        <livewire:product-list type="latest" />

        <!-- Random Products Section -->
        <livewire:product-list type="random" />

        <!-- About Us Section -->
        <section class="mb-12 sm:mb-16">
            <div class="bg-white rounded-lg shadow-lg p-6 sm:p-8 md:p-10">
                <div class="max-w-4xl mx-auto text-center">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">Tentang {{ global_config('site_name') }}</h2>
                    <div class="text-gray-700 text-sm text-center leading-relaxed space-y-3 sm:space-y-4">

                        {!! $aboutSummary !!}
                        
                        <div class="pt-2 text-center">
                            <a href="/about" class="inline-flex items-center text-sm text-green-600 font-semibold hover:text-green-700 transition-colors">
                                Tentang Kami
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
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
        </section>

    <!-- Best Selling Products Section -->
    <livewire:product-list />

    </div>

    <!-- Testimonials Section -->
    <livewire:testimonial-carousel />

    <!-- Special Promo Section -->
    <livewire:voucher-carousel />

    <!-- Footer -->
    <x-footer />

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
