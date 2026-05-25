<div class="bg-white">
    <x-navbar />

    @include('livewire.components.home.hero-carousel', ['banners' => $banners])
    
    {{-- @include('livewire.components.home.tier-slider') --}}

    <livewire:components.flash-sale-strip />

    @include('livewire.components.home.voucher-carousel', ['vouchers' => $vouchers])

    <livewire:product-list type="latest" />

    <livewire:product-list type="random" />

    @include('livewire.components.home.about-block', ['aboutSummary' => $aboutSummary])

    @include('livewire.components.home.blog-mosaic', ['posts' => $blogPosts])

    <livewire:product-list type="best-selling" />

    @include('livewire.components.home.testimonial-section')

    @include('livewire.components.home.quick-cat-strip', ['categories' => $categories])

    @include('livewire.components.home.newsletter-cta')

    <x-footer :categories="$categories" />
</div>
