<div class="bg-gray-50 min-h-screen" style="font-family: 'Poppins', sans-serif;">
    <!-- Navigation -->
    <x-navbar />

    <!-- Breadcrumb -->
    <div class="pt-16 sm:pt-20 pb-3 sm:pb-4 bg-white border-b">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm">
                    <li><a href="/" class="text-gray-500 hover:text-gray-700">Beranda</a></li>
                    <li><span class="text-gray-400">/</span></li>
                    <li><a href="{{ route('blog.archives') }}" class="text-gray-500 hover:text-gray-700">Blog</a></li>
                    <li><span class="text-gray-400">/</span></li>
                    <li><span class="text-gray-900 font-medium truncate max-w-[12ch]" title="{{ $post->title }}">{{ Str::limit($post->title, 45) }}</span></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-10 sm:py-10">
        <!-- Blog Post Content -->
        <div class="bg-white rounded-lg shadow-lg p-6 sm:p-8 md:p-10 mb-8">

            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                <span class="px-2 sm:px-3 py-1 text-xs font-semibold text-blue-600 bg-blue-50 rounded-full">
                    {{ $post->category->name }}
                </span>
                <span class="text-xs sm:text-sm text-gray-500">
                    {{ $post->published_at->format('d F Y') }}
                </span>
            </div>

            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 mb-6 sm:mb-8">{{ $post->title }}</h1>

            <!-- Thumbnail -->
            @if($post->thumbnail)
                <div class="aspect-video w-full overflow-hidden rounded-lg mb-6 sm:mb-8">
                    <img 
                        src="{{ asset('storage/' . $post->thumbnail) }}" 
                        alt="{{ $post->title }}"
                        class="w-full h-full object-cover"
                    >
                </div>
            @endif

            <!-- Content -->
            <div class="prose max-w-none text-gray-700 text-sm sm:text-[15px] space-y-4">
                {!! $post->content !!}
            </div>
        </div>

        <!-- Related Posts -->
        @if($relatedPosts->isNotEmpty())
            <div class="bg-white rounded-lg shadow-lg p-6 sm:p-8">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-6">Artikel Terkait</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($relatedPosts as $relatedPost)
                        <article class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                            @if($relatedPost->thumbnail)
                                <a href="{{ route('blog.detail', $relatedPost->slug) }}" class="block aspect-video overflow-hidden">
                                    <img 
                                        src="{{ asset('storage/' . $relatedPost->thumbnail) }}" 
                                        alt="{{ $relatedPost->title }}"
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                    >
                                </a>
                            @endif

                            <div class="p-3 sm:p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2 py-1 text-xs font-semibold text-blue-600 bg-blue-50 rounded-full">
                                        {{ $relatedPost->category->name }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $relatedPost->published_at->format('d M Y') }}
                                    </span>
                                </div>
                                
                                <h3 class="text-sm sm:text-base font-bold text-gray-900 mb-2 line-clamp-2 hover:text-blue-600 transition-colors">
                                    <a href="{{ route('blog.detail', $relatedPost->slug) }}">
                                        {{ $relatedPost->title }}
                                    </a>
                                </h3>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <x-footer />
</div>
