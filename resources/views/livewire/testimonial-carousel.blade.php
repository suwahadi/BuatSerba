<div>
@if($testimonials->count() > 0)
<div wire:ignore>
    <div x-data="{
            activeSlide: 0,
            visible: 2,
            total: {{ $testimonials->count() }},
            timer: null,
            touchStartX: 0,
            touchActive: false,

            init() {
                this.updateVisible();
                window.addEventListener('resize', () => this.updateVisible());
                this.startAuto();
                document.addEventListener('visibilitychange', () => {
                    document.hidden ? this.stopAuto() : this.startAuto();
                });
            },

            updateVisible() {
                this.visible = window.innerWidth >= 768 ? 4 : 2;
                if (this.activeSlide > this.maxSlide) this.activeSlide = this.maxSlide;
            },

            get maxSlide() {
                return Math.max(0, this.total - this.visible);
            },

            next() {
                if (this.maxSlide === 0) return;
                this.activeSlide = this.activeSlide >= this.maxSlide ? 0 : this.activeSlide + 1;
            },

            prev() {
                if (this.maxSlide === 0) return;
                this.activeSlide = this.activeSlide <= 0 ? this.maxSlide : this.activeSlide - 1;
            },

            startAuto() {
                this.stopAuto();
                if (this.maxSlide === 0) return;
                this.timer = setInterval(() => this.next(), 3000);
            },

            stopAuto() {
                if (this.timer) { clearInterval(this.timer); this.timer = null; }
            },

            handleTouchStart(e) {
                this.touchStartX = e.touches[0].clientX;
                this.touchActive = true;
                this.stopAuto();
            },

            handleTouchEnd(e) {
                if (!this.touchActive) return;
                this.touchActive = false;
                const dx = e.changedTouches[0].clientX - this.touchStartX;
                if (Math.abs(dx) > 40) {
                    if (dx > 0) this.prev(); else this.next();
                }
                this.startAuto();
            }
        }"
        @mouseenter="stopAuto"
        @mouseleave="startAuto"
        class="relative">

        <div class="overflow-hidden"
             @touchstart="handleTouchStart($event)"
             @touchend="handleTouchEnd($event)">
            <div class="flex transition-transform duration-500 ease-out"
                 :style="`transform: translateX(-${activeSlide * (100 / visible)}%)`">
                @foreach($testimonials as $testimonial)
                    <div class="shrink-0 px-1.5 md:px-2"
                         :style="`width: ${100/visible}%`">
                        <div class="aspect-[3/4] rounded-xl overflow-hidden bg-paper shadow-card">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($testimonial->image) }}"
                                 alt="Testimoni pembeli"
                                 class="w-full h-full object-cover select-none"
                                 draggable="false"
                                 loading="lazy">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <template x-if="maxSlide > 0">
            <div>
                <button type="button"
                        @click="prev(); startAuto()"
                        class="hidden sm:grid place-items-center absolute left-0 md:-left-2 top-1/2 -translate-y-1/2 w-9 h-9 md:w-10 md:h-10 rounded-full bg-white shadow-card text-ink hover:bg-paper z-10"
                        aria-label="Testimoni sebelumnya">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"/></svg>
                </button>
                <button type="button"
                        @click="next(); startAuto()"
                        class="hidden sm:grid place-items-center absolute right-0 md:-right-2 top-1/2 -translate-y-1/2 w-9 h-9 md:w-10 md:h-10 rounded-full bg-white shadow-card text-ink hover:bg-paper z-10"
                        aria-label="Testimoni berikutnya">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg>
                </button>
            </div>
        </template>
    </div>
</div>
@endif
</div>
