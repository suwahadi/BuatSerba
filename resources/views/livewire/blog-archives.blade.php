<div class="bg-white">
    <x-navbar />

    {{-- Breadcrumb --}}
    <nav class="border-b border-black/5" aria-label="Breadcrumb">
        <div class="container-x py-2.5">
            <ol class="flex items-center gap-1.5 text-[12px]">
                <li><a href="/" class="text-black/55 hover:text-emerald20-600 transition-colors">Beranda</a></li>
                <li class="text-black/30">/</li>
                <li class="font-semibold text-ink">Blog</li>
            </ol>
        </div>
    </nav>

    {{-- Page header --}}
    <header class="container-x pt-5 sm:pt-7 pb-4">
        <p class="text-[11px] uppercase tracking-[0.2em] text-violet10-500 font-semibold">Artikel &amp; Cerita</p>
        <div class="flex flex-wrap items-end justify-between gap-3 mt-1">
            <div>
                <h1 class="font-display font-extrabold text-[22px] md:text-[30px] tracking-tight text-ink">Blog buatserba</h1>
                <p class="text-[12px] sm:text-[13px] text-black/55 mt-0.5">
                    Tips belanja, panduan produk, &amp; inspirasi gaya hidup terbaru.
                </p>
            </div>
        </div>
    </header>

    <div class="container-x pb-10 md:pb-14">
        @if($posts->isEmpty())
            {{-- Empty state --}}
            <div class="bg-paper/60 border border-black/5 rounded-2xl text-center py-16 px-6 max-w-2xl mx-auto">
                <div class="w-14 h-14 mx-auto rounded-full bg-white border border-black/[0.04] grid place-items-center shadow-card">
                    <svg class="w-7 h-7 text-violet10-500" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
                <h3 class="mt-3 font-display font-bold text-[16px] text-ink">Belum ada artikel</h3>
                <p class="mt-1 text-[13px] text-black/55">Pantau halaman ini untuk artikel &amp; tips dari kami.</p>
            </div>
        @else
            {{-- Blog Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
                @foreach($posts as $post)
                    <article class="pcard bg-white rounded-2xl shadow-card overflow-hidden border border-black/[0.04] focus-within:ring-2 focus-within:ring-violet10-300 flex flex-col">
                        @if($post->thumbnail)
                            <a href="{{ route('blog.detail', $post->slug) }}" class="block aspect-video overflow-hidden bg-paper relative">
                                <img src="{{ asset('storage/' . $post->thumbnail) }}"
                                     alt="{{ $post->title }}"
                                     class="pimg w-full h-full object-cover">
                            </a>
                        @endif

                        <div class="p-4 sm:p-5 flex-1 flex flex-col">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider text-violet10-700 bg-violet10-50 border border-violet10-100">
                                    {{ $post->category->name }}
                                </span>
                                <span class="font-mono text-[11px] text-black/45">
                                    {{ $post->published_at->format('d M Y') }}
                                </span>
                            </div>

                            <h2 class="font-display font-bold text-[15px] sm:text-[16px] leading-snug text-ink line-clamp-2 mb-2 transition-colors hover:text-violet10-700">
                                <a href="{{ route('blog.detail', $post->slug) }}" class="focus:outline-none">
                                    {{ $post->title }}
                                </a>
                            </h2>

                            <p class="text-[13px] text-black/60 line-clamp-3 mb-4 flex-1">
                                {{ Str::limit(strip_tags($post->content), 130) }}
                            </p>

                            <a href="{{ route('blog.detail', $post->slug) }}"
                               class="inline-flex items-center gap-1 text-[12px] font-semibold text-violet10-600 hover:text-violet10-800 transition-colors w-fit group">
                                Baca Selengkapnya
                                <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 12h14"/><path stroke-linecap="round" d="m13 5 7 7-7 7"/></svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Load More --}}
            @if($hasMore)
                <div class="mt-8 sm:mt-10 text-center">
                    <button wire:click="loadMore" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-black/10 rounded-xl text-[13px] font-semibold text-ink hover:border-violet10-500 hover:text-violet10-700 transition-colors shadow-card disabled:opacity-50">
                        <span wire:loading.remove wire:target="loadMore" class="inline-flex items-center gap-2">
                            Muat Artikel Lainnya
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                        <span wire:loading wire:target="loadMore" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-violet10-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Memuat...
                        </span>
                    </button>
                </div>
            @endif
        @endif
    </div>

    <x-footer />
</div>
