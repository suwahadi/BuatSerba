<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Modifiers\ResizeModifier;
use Intervention\Image\Encoders\WebpEncoder;
class ProductImageObserver
{
    /**
     * Handle the Product "saved" event.
     */
    public function saved(Product $product): void
    {
        $path = $product->getAttribute('main_image');

        if (! $path) {
            return;
        }

        $path = ltrim($path, '/');

        $disk = Storage::disk('public');

        $possibleLocalPaths = [
            $disk->path($path),
            public_path('storage/' . $path),
            storage_path('app/public/' . $path),
        ];

        $localPath = null;

        foreach ($possibleLocalPaths as $p) {
            if ($p && file_exists($p)) {
                $localPath = $p;
                break;
            }
        }

        if (! $localPath) {
            return;
        }

        $extension = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));

        if ($extension === 'webp') {
            return;
        }

        try {
            $image = Image::read($localPath);
            $image->orient();

            $width = $image->width();
            $height = $image->height();

            $maxSide = max($width, $height);
            if ($maxSide > 800) {
                $ratio = 800 / $maxSide;
                $newW = (int) round($width * $ratio);
                $newH = (int) round($height * $ratio);
                $image->modify(new ResizeModifier($newW, $newH));
            }

            $encoded = $image->encode(new WebpEncoder(quality: 80));
            $webpData = $encoded->toString();

            $newPath = preg_replace('/\.[^.]+$/', '.webp', $path);

            $disk->put($newPath, $webpData);

            if ($newPath !== $path && $disk->exists($path)) {
                $disk->delete($path);
            }

            if ($newPath !== $path) {
                $product->forceFill(['main_image' => $newPath])->saveQuietly();
            }
        } catch (\Throwable $e) {
            Log::error('ProductImageObserver: exception processing image: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Product "saving" event so the uploaded image is processed
     * before the model is persisted. This ensures Filament's form receives
     * the final path immediately and the upload preview doesn't appear empty.
     */
    public function saving(Product $product): void
    {
        $path = $product->getAttribute('main_image');

        if (! $path) {
            return;
        }

        $path = ltrim($path, '/');

        $disk = Storage::disk('public');

        $possibleLocalPaths = [
            $disk->path($path),
            public_path('storage/' . $path),
            storage_path('app/public/' . $path),
        ];

        $localPath = null;
        foreach ($possibleLocalPaths as $p) {
            if ($p && file_exists($p)) {
                $localPath = $p;
                break;
            }
        }

        if (! $localPath) {
            return;
        }

        $extension = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
        if ($extension === 'webp') {
            return;
        }

        try {
            $image = Image::read($localPath);
            $image->orient();

            $width = $image->width();
            $height = $image->height();
            $maxSide = max($width, $height);
            if ($maxSide > 800) {
                $ratio = 800 / $maxSide;
                $newW = (int) round($width * $ratio);
                $newH = (int) round($height * $ratio);
                $image->modify(new ResizeModifier($newW, $newH));
            }

            $encoded = $image->encode(new WebpEncoder(quality: 80));
            $webpData = $encoded->toString();

            $newPath = preg_replace('/\.[^.]+$/', '.webp', $path);
            $disk->put($newPath, $webpData);

            if ($newPath !== $path && $disk->exists($path)) {
                $disk->delete($path);
            }

            $product->setAttribute('main_image', $newPath);
        } catch (\Throwable $e) {
        }
    }
}
