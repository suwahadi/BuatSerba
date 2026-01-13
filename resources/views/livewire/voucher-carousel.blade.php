<div>
@if($vouchers->count() > 0)
<div class="py-12 bg-white" wire:ignore>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Promo Spesial</h2>
        </div>
        
        <div x-data="{
            activeSlide: 0,
            originalTotal: {{ $vouchers->count() }},
            visible: 3,
            autoSlideInterval: null,
            transitioning: true,
            lightboxOpen: false,
            lightboxImage: '',
            
            init() {
                this.updateVisible();
                window.addEventListener('resize', () => this.updateVisible());
                this.startAutoSlide();
            },
            
            updateVisible() {
                if (window.innerWidth >= 1024) this.visible = 3;
                else if (window.innerWidth >= 768) this.visible = 2;
                else this.visible = 1;
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
                }, 4000);
            },
            
            stopAutoSlide() {
                if (this.autoSlideInterval) {
                    clearInterval(this.autoSlideInterval);
                    this.autoSlideInterval = null;
                }
            },

            openLightbox(url) {
                this.lightboxImage = url;
                this.lightboxOpen = true;
                this.stopAutoSlide();
            },

            closeLightbox() {
                this.lightboxOpen = false;
                this.startAutoSlide();
                setTimeout(() => {
                    this.lightboxImage = '';
                }, 300);
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
                        // Kita ambil 3 item pertama (max visible) dan taruh di belakang
                        $allVouchers = $vouchers->concat($vouchers->take(3));
                    @endphp

                    @foreach($allVouchers as $voucher)
                        <div class="flex-shrink-0 px-2"
                             :style="`width: ${100/visible}%`">
                            <div class="bg-gray-100 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow aspect-[16/6] md:aspect-[21/9]">
                                <img src="{{ image_url($voucher->image) }}" 
                                     alt="{{ $voucher->voucher_name }}" 
                                     class="w-full h-full object-cover cursor-zoom-in"
                                     @click="openLightbox('{{ image_url($voucher->image) }}')">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Prev Button -->
            <button @click="prev" 
                    class="absolute left-0 top-1/2 -translate-y-1/2 -ml-2 lg:-ml-4 bg-white hover:bg-gray-50 text-gray-800 p-2 rounded-full shadow-lg border border-gray-100 opacity-0 group-hover:opacity-100 transition-opacity z-10 disabled:opacity-50"
                    aria-label="Previous slide">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <!-- Next Button -->
            <button @click="next" 
                    class="absolute right-0 top-1/2 -translate-y-1/2 -mr-2 lg:-mr-4 bg-white hover:bg-gray-50 text-gray-800 p-2 rounded-full shadow-lg border border-gray-100 opacity-0 group-hover:opacity-100 transition-opacity z-10 disabled:opacity-50"
                    aria-label="Next slide">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            
            <!-- Lightbox Modal -->
            <template x-teleport="body">
                <div x-show="lightboxOpen" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm"
                    @click="closeLightbox"
                    @keydown.escape.window="closeLightbox"
                    style="display: none;">
                    
                    <button @click="closeLightbox" class="absolute top-4 right-4 text-white hover:text-gray-300 z-50 p-2 focus:outline-none transition-transform hover:scale-110">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <div class="relative max-w-7xl w-full h-full flex items-center justify-center p-2 sm:p-6" @click.stop>
                        <img :src="lightboxImage" 
                            class="max-w-full max-h-[90vh] rounded-lg shadow-2xl object-contain"
                            alt="Voucher Preview">
                    </div>
                </div>
            </template>
            
        </div>
    </div>
</div>
@endif
</div>
