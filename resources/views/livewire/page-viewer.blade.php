<div class="bg-white">
    <x-navbar />

    {{-- Breadcrumb — seragam dengan catalog/product/cart/checkout/blog --}}
    <nav class="border-b border-black/5" aria-label="Breadcrumb">
        <div class="container-x py-2.5 overflow-x-auto">
            <ol class="flex items-center gap-1.5 text-[12px] whitespace-nowrap">
                <li><a href="/" class="text-black/55 hover:text-emerald20-600 transition-colors">Beranda</a></li>
                <li class="text-black/30">/</li>
                <li class="font-semibold text-ink truncate max-w-[200px] sm:max-w-none">{{ $page->title }}</li>
            </ol>
        </div>
    </nav>

    <div class="container-x py-5 sm:py-8 md:py-10">
        <article>
            {{-- Eyebrow --}}
            <p class="text-[11px] uppercase tracking-[0.2em] text-tan5-600 font-semibold">Halaman Informasi</p>

            {{-- Title --}}
            <h1 class="font-display font-extrabold text-[22px] sm:text-[28px] md:text-[32px] leading-tight tracking-tight text-ink mt-1">
                {{ $page->title }}
            </h1>

            {{-- Content --}}
            <div class="mt-6 sm:mt-8 rich-content text-ink/80 text-[14px] sm:text-[15px] leading-relaxed @if($page->slug === 'about') text-justify @endif">
                {!! nl2br($page->content) !!}
            </div>
        </article>
    </div>

    <x-footer />

    <style>
        /* Brand-styled rich content (CMS page typography) — selaras dengan blog-detail */
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
        .rich-content blockquote { border-left: 3px solid var(--color-emerald20-200); padding-left: 1em; color: var(--color-tan5-700); font-style: italic; }
        .rich-content a { color: var(--color-emerald20-700); text-decoration: underline; text-underline-offset: 2px; }
        .rich-content a:hover { color: var(--color-emerald20-800); }
        .rich-content img { max-width: 100%; height: auto; border-radius: 0.75rem; margin: 1em 0; }
        .rich-content hr { border: none; border-top: 1px solid rgba(0,0,0,0.08); margin: 1.5em 0; }
        .rich-content pre { background: var(--color-paper); padding: 0.75em 1em; border-radius: 0.5rem; overflow-x: auto; font-family: var(--font-mono); font-size: 0.9em; }
        .rich-content code { background: var(--color-paper); padding: 0.1em 0.35em; border-radius: 0.25rem; font-family: var(--font-mono); font-size: 0.9em; color: var(--color-tan5-700); }
        .rich-content table { width: 100%; border-collapse: collapse; font-size: 0.95em; }
        .rich-content th, .rich-content td { border: 1px solid rgba(0,0,0,0.08); padding: 0.5em 0.75em; text-align: left; }
        .rich-content th { background: var(--color-paper); font-weight: 600; color: var(--color-ink); }
    </style>
</div>
