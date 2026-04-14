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
                    <li><span class="text-gray-900 font-medium">Blog</span></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-10 sm:py-10">

        @if($posts->isEmpty())
            <div class="bg-white rounded-lg shadow-lg p-8 sm:p-12 text-center">
                <p class="text-gray-500 text-sm sm:text-base">Belum ada artikel yang tersedia.</p>
            </div>
        @else
            <!-- Blog Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($posts as $post)
                    <article class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        @if($post->thumbnail)
                            <a href="{{ route('blog.detail', $post->slug) }}" class="block aspect-video overflow-hidden">
                                <img 
                                    src="{{ asset('storage/' . $post->thumbnail) }}" 
                                    alt="{{ $post->title }}"
                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                >
                            </a>
                        @endif
                        
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-2 sm:px-3 py-1 text-xs font-semibold text-blue-600 bg-blue-50 rounded-full">
                                    {{ $post->category->name }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $post->published_at->format('d M Y') }}
                                </span>
                            </div>
                            
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-3 line-clamp-2 hover:text-blue-600 transition-colors">
                                <a href="{{ route('blog.detail', $post->slug) }}">
                                    {{ $post->title }}
                                </a>
                            </h2>
                            
                            <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                                {{ Str::limit(strip_tags($post->content), 120) }}
                            </p>
                            
                            <a 
                                href="{{ route('blog.detail', $post->slug) }}"
                                class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium text-sm"
                            >
                                Baca Selengkapnya
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Load More Button -->
            @if($hasMore)
                <div class="mt-8 sm:mt-10 text-center">
                    <button 
                        wire:click="loadMore"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-sm sm:text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <span wire:loading.remove wire:target="loadMore">Lebih Banyak</span>
                        <span wire:loading wire:target="loadMore">Memuat...</span>
                    </button>
                </div>
            @endif
        @endif
    </div>

    <!-- Footer -->
    <x-footer />
</div>
