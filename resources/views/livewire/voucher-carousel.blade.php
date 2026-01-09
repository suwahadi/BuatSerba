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
                                     class="w-full h-full object-cover">
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
            
        </div>
    </div>
</div>
@endif
</div>
