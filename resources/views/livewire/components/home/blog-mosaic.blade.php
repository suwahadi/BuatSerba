{{--
    "Blog Terbaru Kami"
    Desktop: 5-cell masonry mosaic (hover-revealed title).
    Mobile: simple 2-column grid showing 6 posts with always-visible title.
    Empty cells (when fewer posts) fall back to placeholder + link to blog archives.
--}}
@php
    $posts = $posts ?? collect();
    $placeholderImg = asset('images/placeholder.jpg');
    $desktopLayouts = [
        'col-span-3 row-span-2',
        'col-span-2 row-span-1',
        'col-span-1 row-span-1',
        'col-span-1 row-span-1',
        'col-span-2 row-span-1',
    ];
    $mobileCount = 6;
@endphp
<section aria-labelledby="blog-mosaic-h" class="container-x mt-14 md:mt-24">
    <div class="flex items-end justify-between flex-wrap gap-3 mb-5 md:mb-6">
        <div>
            <p class="text-[12px] uppercase tracking-[0.25em] text-tan5-600 font-semibold">Latest Update</p>
            <h2 id="blog-mosaic-h" class="font-display font-extrabold text-[26px] md:text-[40px] leading-tight tracking-tight mt-1 text-ink">Blog Terbaru Kami</h2>
        </div>
        <a href="{{ route('blog.archives') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald20-700 hover:text-emerald20-800 transition-colors">
            Lihat Semua
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
    </div>

    {{-- Desktop mosaic (unchanged) --}}
    <div class="hidden md:grid grid-cols-6 grid-rows-2 gap-4 h-[420px]">
        @foreach($desktopLayouts as $i => $layout)
            @php $post = $posts[$i] ?? null; @endphp
            @if($post)
                <a href="{{ route('blog.detail', $post->slug) }}"
                   class="{{ $layout }} relative rounded-2xl overflow-hidden ph-{{ $i + 1 }} group block"
                   aria-label="{{ $post->title }}">
                    <img loading="lazy"
                         src="{{ image_url($post->thumbnail, $placeholderImg) }}"
                         alt="{{ $post->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                         onerror="this.onerror=null;this.src='{{ $placeholderImg }}'"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute bottom-3 left-3 right-3 text-white opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity duration-300">
                        @if($post->category)
                            <span class="inline-flex px-2 py-0.5 rounded-full bg-white/20 backdrop-blur text-[10px] font-semibold uppercase tracking-wider">{{ $post->category->name }}</span>
                        @endif
                        <p class="font-display font-bold text-[14px] md:text-[16px] mt-1.5 line-clamp-2 leading-tight">{{ $post->title }}</p>
                    </div>
                </a>
            @else
                <a href="{{ route('blog.archives') }}"
                   class="{{ $layout }} relative rounded-2xl overflow-hidden ph-{{ $i + 1 }} group block bg-paper"
                   aria-label="Lihat semua artikel blog">
                    <img loading="lazy"
                         src="{{ $placeholderImg }}"
                         alt="Blog placeholder"
                         class="w-full h-full object-cover opacity-70 group-hover:opacity-90 transition-opacity"/>
                    <div class="absolute inset-0 grid place-items-center text-black/45 text-[11px] md:text-[12px] font-semibold uppercase tracking-[0.2em]">Coming Soon</div>
                </a>
            @endif
        @endforeach
    </div>

    {{-- Mobile: 2-column grid, always-visible title --}}
    <div class="grid grid-cols-2 gap-2 md:hidden">
        @for($i = 0; $i < $mobileCount; $i++)
            @php $post = $posts[$i] ?? null; @endphp
            @if($post)
                <a href="{{ route('blog.detail', $post->slug) }}"
                   class="relative aspect-square rounded-2xl overflow-hidden block"
                   aria-label="{{ $post->title }}">
                    <img loading="lazy"
                         src="{{ image_url($post->thumbnail, $placeholderImg) }}"
                         alt="{{ $post->title }}"
                         class="w-full h-full object-cover"
                         onerror="this.onerror=null;this.src='{{ $placeholderImg }}'"/>
                    <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-black/85 via-black/45 to-transparent"></div>
                    <div class="absolute bottom-2.5 left-2.5 right-2.5 text-white">
                        @if($post->category)
                            <span class="inline-flex px-1.5 py-0.5 rounded-full bg-white/20 backdrop-blur text-[9px] font-semibold uppercase tracking-wider">{{ $post->category->name }}</span>
                        @endif
                        <p class="font-display font-bold text-[13px] mt-1 line-clamp-2 leading-tight">{{ $post->title }}</p>
                    </div>
                </a>
            @else
                <a href="{{ route('blog.archives') }}"
                   class="relative aspect-square rounded-2xl overflow-hidden block bg-paper"
                   aria-label="Lihat semua artikel blog">
                    <img loading="lazy"
                         src="{{ $placeholderImg }}"
                         alt="Blog placeholder"
                         class="w-full h-full object-cover opacity-70"/>
                    <div class="absolute inset-0 grid place-items-center text-black/45 text-[11px] font-semibold uppercase tracking-[0.2em]">Coming Soon</div>
                </a>
            @endif
        @endfor
    </div>
</section>
