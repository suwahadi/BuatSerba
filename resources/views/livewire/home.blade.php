<div class="bg-gray-50">
    <x-navbar />

    <div class="pt-12 md:pt-16">
        <div class="relative overflow-hidden bg-gray-200">
            <div class="hero-slider" id="heroSlider">
                @forelse($banners as $index => $banner)
                    <div class="hero-slide {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}">
                        <div class="relative w-full aspect-auto md:aspect-[1600/600]">
                            @if($banner->url)
                                <a href="{{ $banner->url }}" class="block w-full h-full">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($banner->image) }}" 
                                         alt="{{ $banner->title }}" 
                                         class="w-full h-full object-contain md:object-cover select-none"
                                         draggable="false">
                                </a>
                            @else
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($banner->image) }}" 
                                     alt="{{ $banner->title }}" 
                                     class="w-full h-full object-contain md:object-cover select-none"
                                     draggable="false">
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="hero-slide active" data-slide="0">
                        <div class="relative w-full aspect-auto md:aspect-[1600/600]">
                            <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?q=80&w=1600&h=600&fit=crop" 
                                 alt="Default Banner" 
                                 class="w-full h-full object-contain md:object-cover select-none"
                                 draggable="false">
                        </div>
                    </div>
                @endforelse

                <!-- Slider Navigation -->
                @if($banners->count() > 1)
                <div class="absolute bottom-4 sm:bottom-6 left-1/2 -translate-x-1/2 flex space-x-2 z-10" id="sliderIndicators">
                    @foreach($banners as $index => $banner)
                        <button class="slider-indicator h-2 sm:h-2.5 rounded-full transition-all duration-300 {{ $index === 0 ? 'active' : '' }}" 
                                data-slide="{{ $index }}"
                                aria-label="Go to slide {{ $index + 1 }}">
                        </button>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 pt-6 sm:pt-8 md:pt-12 pb-3 sm:pb-4 md:pb-6 home-page-container">

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
                                <div class="text-green-600 mb-3 sm:mb-3">
                                    <svg class="w-16 h-16 sm:w-12 sm:h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-1 sm:mb-2">Produk Berkualitas</h3>
                                <p class="text-xs sm:text-sm text-gray-600">100% produk original dan terjamin kualitasnya</p>
                            </div>
                            <div class="p-4 sm:p-6 bg-green-50 rounded-lg">
                                <div class="text-green-600 mb-3 sm:mb-3">
                                    <svg class="w-16 h-16 sm:w-12 sm:h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-1 sm:mb-2">Harga Terbaik</h3>
                                <p class="text-xs sm:text-sm text-gray-600">Harga kompetitif dengan penawaran spesial</p>
                            </div>
                            <div class="p-4 sm:p-6 bg-green-50 rounded-lg">
                                <div class="text-green-600 mb-3 sm:mb-3">
                                    <svg class="w-16 h-16 sm:w-12 sm:h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    <livewire:product-list class="!mb-0" />

    </div>

    <!-- Testimonials Section -->
    <livewire:testimonial-carousel class="-mt-[50px]" />

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

        @media (max-width: 640px) {
            .home-page-container {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
                padding-top: 1rem !important;
                padding-bottom: 0.75rem !important;
            }
            
            .home-page-container .grid.grid-cols-2.md\:grid-cols-3.lg\:grid-cols-6 {
                gap: 0.5rem !important;
            }
            
            .home-page-container .bg-white.rounded-lg .p-3 {
                padding: 0.5rem !important;
            }

            .home-page-container section h2 {
                font-size: 0.875rem !important;
            }
            
            .home-page-container .bg-white.rounded-lg h3 {
                font-size: 0.75rem !important;
                line-height: 1.25 !important;
                height: 2rem !important;
            }

            .home-page-container .grid .bg-green-50 h3 {
                height: auto !important;
                margin-bottom: 0.25rem !important;
            }
            
            .home-page-container .bg-white.rounded-lg .text-xs {
                font-size: 0.625rem !important;
            }
            
            .home-page-container .bg-white.rounded-lg .text-sm {
                font-size: 0.6875rem !important;
            }
            
            .home-page-container .bg-white.rounded-lg svg {
                width: 0.625rem !important;
                height: 0.625rem !important;
            }
            
            .home-page-container .grid .bg-green-50 svg {
                width: 3rem !important;
                height: 3rem !important;
            }
            
            .home-page-container a.text-sm {
                font-size: 0.6875rem !important;
            }
        }

        .hero-slider {
            position: relative;
        }
        .hero-slide {
            display: none;
            opacity: 0;
            transition: opacity 0.7s ease-in-out;
        }
        .hero-slide.active {
            display: block;
            opacity: 1;
        }

        .slider-indicator {
            width: 0.5rem; /* 2 (8px) */
            background-color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
        }
        .slider-indicator:hover {
            background-color: rgba(255, 255, 255, 0.75);
        }
        .slider-indicator.active {
            width: 1.5rem; /* 6 (24px) */
            background-color: rgb(255, 255, 255);
        }
        
        @media (min-width: 640px) {
            .slider-indicator {
                width: 0.625rem; /* 2.5 (10px) */
            }
            .slider-indicator.active {
                width: 2rem; /* 8 (32px) */
            }
        }
    </style>

    @push('scripts')
    <script>
        (function() {
            let sliderInitialized = false;
            
            function initHeroSlider() {
                if (sliderInitialized) return;
                
                const heroSlider = document.getElementById('heroSlider');
                if (!heroSlider) return;
                
                const slides = heroSlider.querySelectorAll('.hero-slide');
                const indicators = heroSlider.querySelectorAll('.slider-indicator');
                
                if (slides.length === 0) return;
                
                sliderInitialized = true;
                
                let currentSlide = 0;
                let autoPlayInterval = null;
                let touchStartX = 0;
                let touchEndX = 0;
                
                function showSlide(index) {
                    slides.forEach(slide => {
                        slide.classList.remove('active');
                    });
                    indicators.forEach(indicator => {
                        indicator.classList.remove('active');
                    });
                    
                    if (slides[index]) {
                        slides[index].classList.add('active');
                    }
                    if (indicators[index]) {
                        indicators[index].classList.add('active');
                    }
                    
                    currentSlide = index;
                }
                
                function nextSlide() {
                    currentSlide = (currentSlide + 1) % slides.length;
                    showSlide(currentSlide);
                }
                
                function prevSlide() {
                    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
                    showSlide(currentSlide);
                }
                
                function startAutoPlay() {
                    stopAutoPlay();
                    if (slides.length > 1) {
                        autoPlayInterval = setInterval(nextSlide, 5000);
                    }
                }
                
                function stopAutoPlay() {
                    if (autoPlayInterval) {
                        clearInterval(autoPlayInterval);
                        autoPlayInterval = null;
                    }
                }
                
                function handleTouchStart(e) {
                    touchStartX = e.touches[0].clientX;
                    stopAutoPlay();
                }
                
                function handleTouchMove(e) {
                    touchEndX = e.touches[0].clientX;
                }
                
                function handleTouchEnd() {
                    const swipeThreshold = 50;
                    const diff = touchStartX - touchEndX;
                    
                    if (Math.abs(diff) > swipeThreshold) {
                        if (diff > 0) {
                            nextSlide();
                        } else {
                            prevSlide();
                        }
                    }
                    
                    startAutoPlay();
                }
                
                indicators.forEach((indicator, index) => {
                    indicator.addEventListener('click', () => {
                        showSlide(index);
                        stopAutoPlay();
                        startAutoPlay();
                    });
                });
                
                heroSlider.addEventListener('touchstart', handleTouchStart, { passive: true });
                heroSlider.addEventListener('touchmove', handleTouchMove, { passive: true });
                heroSlider.addEventListener('touchend', handleTouchEnd);
                
                heroSlider.addEventListener('mouseenter', stopAutoPlay);
                heroSlider.addEventListener('mouseleave', startAutoPlay);
                
                startAutoPlay();
            }
            
            // Initialize on page load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initHeroSlider);
            } else {
                initHeroSlider();
            }
            
            // Re-initialize after Livewire navigates (if using Livewire navigation)
            document.addEventListener('livewire:navigated', function() {
                sliderInitialized = false;
                initHeroSlider();
            });
        })();
    </script>
    @endpush
</div>
