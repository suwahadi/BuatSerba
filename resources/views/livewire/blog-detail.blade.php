<div class="bg-white">
    <x-navbar />

    {{-- Breadcrumb --}}
    <nav class="border-b border-black/5" aria-label="Breadcrumb">
        <div class="container-x py-2.5 overflow-x-auto">
            <ol class="flex items-center gap-1.5 text-[12px] whitespace-nowrap">
                <li><a href="/" class="text-black/55 hover:text-emerald20-600 transition-colors">Beranda</a></li>
                <li class="text-black/30">/</li>
                <li><a href="{{ route('blog.archives') }}" class="text-black/55 hover:text-emerald20-600 transition-colors">Blog</a></li>
                <li class="text-black/30">/</li>
                <li class="font-semibold text-ink truncate max-w-[160px] sm:max-w-[260px]" title="{{ $post->title }}">{{ Str::limit($post->title, 45) }}</li>
            </ol>
        </div>
    </nav>

    <div class="container-x py-5 sm:py-8 md:py-10">
        <article>

            {{-- Meta --}}
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider text-violet10-700 bg-violet10-50 border border-violet10-100">
                    {{ $post->category->name }}
                </span>
                <span class="font-mono text-[12px] text-black/55">
                    {{ $post->published_at->format('d F Y') }}
                </span>
            </div>

            {{-- Title --}}
            <h1 class="font-display font-extrabold text-[22px] sm:text-[28px] md:text-[32px] leading-tight tracking-tight text-ink">
                {{ $post->title }}
            </h1>

            {{-- Thumbnail --}}
            @if($post->thumbnail)
                <div class="mt-6 aspect-video w-full overflow-hidden rounded-2xl bg-paper border border-black/[0.04]">
                    <img src="{{ asset('storage/' . $post->thumbnail) }}"
                         alt="{{ $post->title }}"
                         class="w-full h-full object-cover">
                </div>
            @endif

            {{-- Content --}}
            <div class="mt-6 sm:mt-8 rich-content text-ink/80 text-[14px] sm:text-[15px] leading-relaxed">
                {!! $post->content !!}
            </div>

            {{-- Share / back --}}
            <div class="mt-8 pt-6 border-t border-black/5 flex items-center justify-between">
                <a href="{{ route('blog.archives') }}" class="inline-flex items-center gap-1.5 text-[13px] text-violet10-600 hover:text-violet10-800 font-semibold transition-colors">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" d="M19 12H5"/><path stroke-linecap="round" d="m11 19-7-7 7-7"/></svg>
                    Semua Artikel
                </a>
                <span class="text-[11px] text-black/40 font-mono uppercase tracking-wider">Diterbitkan {{ $post->published_at->diffForHumans() }}</span>
            </div>
        </article>

        {{-- Related Posts --}}
        @if($relatedPosts->isNotEmpty())
            <section class="mt-10 sm:mt-14" aria-label="Artikel terkait">
                <div class="flex items-end justify-between mb-4 md:mb-5">
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.2em] text-violet10-500 font-semibold">Bacaan Lanjutan</p>
                        <h2 class="font-display font-extrabold text-[20px] md:text-[26px] tracking-tight text-ink mt-0.5">Artikel Terkait</h2>
                    </div>
                    <a href="{{ route('blog.archives') }}" class="inline-flex items-center gap-1.5 text-[13px] text-violet10-700 font-semibold hover:underline shrink-0">
                        <span class="hidden sm:inline">Semua Artikel</span>
                        <span class="sm:hidden">Semua</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14"/><path d="m13 5 7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                    @foreach($relatedPosts as $relatedPost)
                        <article class="pcard bg-white rounded-2xl shadow-card overflow-hidden border border-black/[0.04] focus-within:ring-2 focus-within:ring-violet10-300 flex flex-col">
                            @if($relatedPost->thumbnail)
                                <a href="{{ route('blog.detail', $relatedPost->slug) }}" class="block aspect-video overflow-hidden bg-paper">
                                    <img src="{{ asset('storage/' . $relatedPost->thumbnail) }}"
                                         alt="{{ $relatedPost->title }}"
                                         class="pimg w-full h-full object-cover">
                                </a>
                            @endif
                            <div class="p-3 sm:p-4 flex-1 flex flex-col">
                                <div class="flex items-center gap-1.5 mb-1.5 flex-wrap">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider text-violet10-700 bg-violet10-50 border border-violet10-100">
                                        {{ $relatedPost->category->name }}
                                    </span>
                                    <span class="font-mono text-[10px] text-black/45">
                                        {{ $relatedPost->published_at->format('d M Y') }}
                                    </span>
                                </div>
                                <h3 class="font-display font-bold text-[13px] sm:text-[14px] leading-snug text-ink line-clamp-2 hover:text-violet10-700 transition-colors">
                                    <a href="{{ route('blog.detail', $relatedPost->slug) }}" class="focus:outline-none">
                                        {{ $relatedPost->title }}
                                    </a>
                                </h3>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <x-footer />

    <style>
        /* Brand-styled article rich content */
        .rich-content > * + * { margin-top: 1em; }
        .rich-content p { line-height: 1.75; }
        .rich-content strong, .rich-content b { font-weight: 700; color: var(--color-ink); }
        .rich-content em, .rich-content i { font-style: italic; }
        .rich-content h1 { font-family: var(--font-display); font-size: 1.5em; font-weight: 800; color: var(--color-ink); letter-spacing: -0.01em; margin-top: 1.6em; }
        .rich-content h2 { font-family: var(--font-display); font-size: 1.3em; font-weight: 800; color: var(--color-ink); margin-top: 1.5em; }
        .rich-content h3 { font-family: var(--font-display); font-size: 1.1em; font-weight: 700; color: var(--color-ink); margin-top: 1.3em; }
        .rich-content ul { list-style-type: disc; padding-left: 1.4em; }
        .rich-content ol { list-style-type: decimal; padding-left: 1.4em; }
        .rich-content li { margin-bottom: 0.3em; }
        .rich-content blockquote { border-left: 3px solid var(--color-violet10-200); padding-left: 1em; color: var(--color-tan5-700); font-style: italic; }
        .rich-content a { color: var(--color-emerald20-700); text-decoration: underline; text-underline-offset: 2px; }
        .rich-content a:hover { color: var(--color-emerald20-800); }
        .rich-content img { max-width: 100%; height: auto; border-radius: 0.75rem; margin: 1em 0; }
        .rich-content hr { border: none; border-top: 1px solid rgba(0,0,0,0.08); margin: 1.5em 0; }
        .rich-content pre { background: var(--color-paper); padding: 0.75em 1em; border-radius: 0.5rem; overflow-x: auto; font-family: var(--font-mono); font-size: 0.9em; }
        .rich-content code { background: var(--color-paper); padding: 0.1em 0.35em; border-radius: 0.25rem; font-family: var(--font-mono); font-size: 0.9em; color: var(--color-tan5-700); }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</div>
