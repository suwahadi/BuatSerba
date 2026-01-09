<div>
@if($categories->count() > 0)
<div class="mb-12 sm:mb-16" wire:ignore>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Kategori Pilihan</h2>
    </div>
    
    <div x-data="{
        activeSlide: 0,
        originalTotal: {{ $categories->count() }},
        visible: 4,
        autoSlideInterval: null,
        transitioning: true,
        
        init() {
            this.updateVisible();
            window.addEventListener('resize', () => this.updateVisible());
            this.startAutoSlide();
        },
        
        updateVisible() {
            // Force 4 columns on all screen sizes as requested
            this.visible = 4;
        },
        
        next() {
            this.transitioning = true;
            this.activeSlide++;
            
            if (this.activeSlide === this.originalTotal) {
                 setTimeout(() => {
                    this.transitioning = false;
                    this.activeSlide = 0;
                 }, 500); 
            }
        },
        
        prev() {
            if (this.activeSlide === 0) {
                this.transitioning = false;
                this.activeSlide = this.originalTotal;
                setTimeout(() => {
                    this.transitioning = true;
                    this.activeSlide--;
                }, 50);
            } else {
                this.transitioning = true;
                this.activeSlide--;
            }
        },
        
        startAutoSlide() {
            this.stopAutoSlide();
            this.autoSlideInterval = setInterval(() => {
                this.next();
            }, 5000);
        },
        
        stopAutoSlide() {
            if (this.autoSlideInterval) {
                clearInterval(this.autoSlideInterval);
                this.autoSlideInterval = null;
            }
        }
    }"
    @mouseenter="stopAutoSlide"
    @mouseleave="startAutoSlide"
    class="relative group">
        
        <!-- Carousel Container -->
        <div class="overflow-hidden">
            <div class="flex"
                 :class="{ 'transition-transform duration-500 ease-in-out': transitioning }"
                 :style="`transform: translateX(-${activeSlide * (100 / visible)}%)`">
                
                @php
                    // Duplikasi items untuk efek infinite loop
                    // Kita ambil 6 item pertama (max visible) dan taruh di belakang
                    // Updated to 4 since max visible is now 4
                    $allCategories = $categories->concat($categories->take(4));
                @endphp

                @foreach($allCategories as $category)
                    <div class="flex-shrink-0 px-2"
                         :style="`width: ${100/visible}%`">
                        <a href="/{{ $category->slug }}" class="block group/item relative rounded-lg overflow-hidden aspect-square shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <!-- Background Image with Overlay -->
                            <div class="absolute inset-0">
                                <img src="{{ image_url($category->image) }}" 
                                     alt="{{ $category->name }}" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover/item:scale-110"
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27400%27 height=%27400%27%3E%3Crect width=%27400%27 height=%27400%27 fill=%27%23374151%27/%3E%3C/svg%3E'">
                                <div class="absolute inset-0 bg-black/40 group-hover/item:bg-black/50 transition-colors duration-300"></div>
                            </div>
                            
                            <!-- Category Name -->
                            <div class="absolute inset-0 flex items-center justify-center p-2 sm:p-3 text-center">
                                <h3 class="text-white font-bold text-[10px] leading-tight sm:text-base lg:text-lg tracking-wide drop-shadow-md group-hover/item:scale-105 transition-transform duration-300">
                                    {{ $category->name }}
                                </h3>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Navigation Buttons -->
        <!-- Prev Button -->
        <button @click="prev" 
                class="absolute left-0 top-1/2 -translate-y-1/2 -ml-2 lg:-ml-4 bg-white hover:bg-green-50 text-gray-800 hover:text-green-600 p-2 rounded-full shadow-lg border border-gray-100 opacity-0 group-hover:opacity-100 transition-all z-10 hover:scale-110 focus:outline-none"
                aria-label="Previous category">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        
        <!-- Next Button -->
        <button @click="next" 
                class="absolute right-0 top-1/2 -translate-y-1/2 -mr-2 lg:-mr-4 bg-white hover:bg-green-50 text-gray-800 hover:text-green-600 p-2 rounded-full shadow-lg border border-gray-100 opacity-0 group-hover:opacity-100 transition-all z-10 hover:scale-110 focus:outline-none"
                aria-label="Next category">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
        
    </div>
</div>
@endif
</div>
