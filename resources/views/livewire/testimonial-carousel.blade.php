<div class="-mt-[30px]">
@if($testimonials->count() > 0)
<div class="pb-12 pt-0 bg-gray-50" wire:ignore>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Apa Kata Mereka?</h2>
            <p class="text-gray-600">Testimoni pelanggan setia {{ global_config('site_name') }}</p>
        </div>
        
        <div x-data="{
            activeSlide: 0,
            total: {{ $testimonials->count() }},
            visible: 3,
            autoSlideInterval: null,
            
            init() {
                this.updateVisible();
                window.addEventListener('resize', () => this.updateVisible());
                this.startAutoSlide();
            },
            
            updateVisible() {
                if (window.innerWidth >= 1024) this.visible = 3;
                else if (window.innerWidth >= 768) this.visible = 2;
                else this.visible = 1;
                
                // Ensure activeSlide is valid after resize
                if (this.activeSlide > this.maxSlide) {
                    this.activeSlide = this.maxSlide;
                }
            },
            
            get maxSlide() {
                return Math.max(0, this.total - this.visible);
            },
            
            next() {
                if (this.activeSlide >= this.maxSlide) {
                    this.activeSlide = 0;
                } else {
                    this.activeSlide++;
                }
            },
            
            prev() {
                if (this.activeSlide <= 0) {
                    this.activeSlide = this.maxSlide;
                } else {
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
        class="relative px-4 sm:px-12 group">
            
            <!-- Carousel Container -->
            <div class="overflow-hidden p-2 -m-2"> <!-- Negative margin to allow shadow to show -->
                <div class="flex transition-transform duration-500 ease-in-out"
                     :style="`transform: translateX(-${activeSlide * (100 / visible)}%)`">
                    @foreach($testimonials as $testimonial)
                        <div class="flex-shrink-0 px-3 w-full md:w-1/2 lg:w-1/3"
                             :style="`width: ${100/visible}%`">
                            <div class="bg-white rounded-xl border border-gray-100 shadow-lg hover:shadow-xl transition-shadow p-6 h-full flex flex-col relative">
                                <!-- Quote Icon -->
                                <div class="absolute top-4 right-6 text-green-100">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017C19.5693 16 20.017 15.5523 20.017 15V9C20.017 8.44772 19.5693 8 19.017 8H15.017C14.4647 8 14.017 8.44772 14.017 9V11C14.017 11.5523 13.5693 12 13.017 12H12.017V5H22.017V15C22.017 18.3137 19.3307 21 16.017 21H14.017ZM5.0166 21L5.0166 18C5.0166 16.8954 5.91203 16 7.0166 16H10.0166C10.5689 16 11.0166 15.5523 11.0166 15V9C11.0166 8.44772 10.5689 8 10.0166 8H6.0166C5.46432 8 5.0166 8.44772 5.0166 9V11C5.0166 11.5523 4.56889 12 4.0166 12H3.0166V5H13.0166V15C13.0166 18.3137 10.3303 21 7.0166 21H5.0166Z"></path>
                                    </svg>
                                </div>

                                <div class="flex items-center mb-4">
                                    <div class="flex-shrink-0">
                                        @if($testimonial->image)
                                            <img class="h-12 w-12 rounded-full object-cover border-2 border-green-100" src="{{ \Illuminate\Support\Facades\Storage::url($testimonial->image) }}" alt="{{ $testimonial->name }}">
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold text-lg border-2 border-green-200">
                                                {{ substr($testimonial->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-sm font-bold text-gray-900">{{ $testimonial->name }}</h4>
                                        <p class="text-xs text-gray-500">{{ $testimonial->location }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex-1">
                                    <p class="text-gray-600 text-sm italic leading-relaxed">
                                        "{{ $testimonial->content }}"
                                    </p>
                                </div>
                                
                                <div class="mt-4 flex text-yellow-400">
                                    @for($i = 0; $i < 5; $i++)
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Prev Button -->
            <button @click="prev" 
                    class="absolute left-0 top-1/2 -translate-y-1/2 bg-white hover:bg-green-50 text-gray-700 hover:text-green-600 p-2 sm:p-3 rounded-full shadow-lg border border-gray-100 z-10 transition-all hover:scale-110 focus:outline-none"
                    aria-label="Previous testimonial">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <!-- Next Button -->
            <button @click="next" 
                    class="absolute right-0 top-1/2 -translate-y-1/2 bg-white hover:bg-green-50 text-gray-700 hover:text-green-600 p-2 sm:p-3 rounded-full shadow-lg border border-gray-100 z-10 transition-all hover:scale-110 focus:outline-none"
                    aria-label="Next testimonial">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            
        </div>
    </div>
</div>
@endif
</div>
