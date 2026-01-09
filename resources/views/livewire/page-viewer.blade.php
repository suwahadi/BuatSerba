<div class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <x-navbar />

    <!-- Breadcrumb -->
    <div class="pt-16 sm:pt-20 pb-3 sm:pb-4 bg-white border-b">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm">
                    <li><a href="/" class="text-gray-500 hover:text-gray-700">Beranda</a></li>
                    <li><span class="text-gray-400">/</span></li>
                    <li><span class="text-gray-900 font-medium">{{ $page->title }}</span></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-10 sm:py-10">
        <!-- Page Content -->
        <div class="bg-white rounded-lg shadow-lg p-3 sm:p-6 md:p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $page->title }}</h1>
            
            <div class="prose max-w-none text-gray-700 text-sm space-y-4">
                {!! nl2br($page->content) !!}
            </div>
        </div>
    </div>

    <!-- Footer -->
    <x-footer />
</div>
