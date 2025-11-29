<?php

if (!function_exists('image_url')) {
    /**
     * Get the full URL for an image.
     * Handles both external URLs and local storage paths.
     *
     * @param string|null $path
     * @param string|null $default
     * @return string
     */
    function image_url(?string $path, ?string $default = null): string
    {
        // If path is null or empty, return default or placeholder
        if (empty($path)) {
            return $default ?? asset('images/placeholder.jpg');
        }

        // If path already starts with http:// or https://, it's an external URL
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // If path starts with 'storage/', use asset() for public storage
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        // Otherwise, assume it's a storage path and use Storage facade
        return \Storage::url($path);
    }
}

if (!function_exists('product_image')) {
    /**
     * Get the product image URL with fallback to placeholder.
     *
     * @param \App\Models\Product|null $product
     * @param string $size ('thumb', 'medium', 'large')
     * @return string
     */
    function product_image($product = null, string $size = 'medium'): string
    {
        if (!$product || !$product->main_image) {
            // Return placeholder based on size
            return asset("images/product-placeholder-{$size}.jpg");
        }

        return image_url($product->main_image);
    }
}

if (!function_exists('format_rupiah')) {
    /**
     * Format number to Rupiah currency format.
     *
     * @param float|int $amount
     * @param bool $includePrefix
     * @return string
     */
    function format_rupiah($amount, bool $includePrefix = true): string
    {
        $formatted = number_format($amount, 0, ',', '.');
        
        return $includePrefix ? "Rp {$formatted}" : $formatted;
    }
}

if (!function_exists('discount_percentage')) {
    /**
     * Calculate discount percentage.
     *
     * @param float|int $originalPrice
     * @param float|int $discountedPrice
     * @return int
     */
    function discount_percentage($originalPrice, $discountedPrice): int
    {
        if ($originalPrice <= 0) {
            return 0;
        }

        $discount = (($originalPrice - $discountedPrice) / $originalPrice) * 100;
        
        return (int) round($discount);
    }
}

if (!function_exists('category_image')) {
    /**
     * Get the category image URL with fallback to placeholder.
     *
     * @param \App\Models\Category|null $category
     * @return string
     */
    function category_image($category = null): string
    {
        if (!$category || !$category->image) {
            return asset('images/category-placeholder.jpg');
        }

        return image_url($category->image);
    }
}

if (!function_exists('user_avatar')) {
    /**
     * Get the user avatar URL with fallback to default avatar.
     *
     * @param \App\Models\User|null $user
     * @return string
     */
    function user_avatar($user = null): string
    {
        if (!$user || !$user->avatar) {
            return asset('images/default-avatar.png');
        }

        return image_url($user->avatar);
    }
}
