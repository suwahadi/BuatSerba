<?php

namespace App\Filament\Resources\MasterProducts\Pages;

use App\Filament\Resources\MasterProducts\MasterProductResource;
use App\Models\Sku;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;

class CreateMasterProduct extends CreateRecord
{
    protected static string $resource = MasterProductResource::class;

    protected function beforeCreate(): void
    {
        // Validate variant SKUs before creating
        $variantsData = $this->data['variants'] ?? [];
        
        foreach ($variantsData as $index => $variant) {
            if (!empty($variant['sku'])) {
                $exists = Sku::where('sku', $variant['sku'])->exists();
                if ($exists) {
                    Notification::make()
                        ->danger()
                        ->title('Error: SKU Duplikat')
                        ->body("Maaf, Kode SKU '{$variant['sku']}' sudah digunakan. Silakan gunakan kode SKU yang berbeda.")
                        ->persistent()
                        ->send();
                    
                    $this->halt();
                }
            }
        }
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Extract SKU data
        $skuData = $data['sku'] ?? [];
        unset($data['sku']);

        // Extract gallery images data
        $galleryImagesData = $data['gallery_images'] ?? [];
        unset($data['gallery_images']);

        // Create product
        $product = static::getModel()::create($data);

        // Create SKU if product created successfully
        if ($product) {
            try {
                Sku::create([
                    'product_id' => $product->id,
                    'sku' => $skuData['sku'] ?? 'SKU-'.strtoupper(Str::random(8)),
                    'unit_cost' => $skuData['unit_cost'] ?? 0,
                    'base_price' => $skuData['base_price'] ?? 0,
                    'selling_price' => $skuData['selling_price'] ?? 0,
                    'stock_quantity' => $skuData['stock_quantity'] ?? 0,
                    'weight' => $skuData['weight'] ?? null,
                    'length' => $skuData['length'] ?? null,
                    'width' => $skuData['width'] ?? null,
                    'height' => $skuData['height'] ?? null,
                    'attributes' => $skuData['attributes'] ?? null,
                    'is_active' => $skuData['is_active'] ?? true,
                ]);
            } catch (UniqueConstraintViolationException $e) {
                // Delete the product if SKU creation fails
                $product->delete();
                
                Notification::make()
                    ->danger()
                    ->title('Error: SKU Duplikat')
                    ->body('Maaf, Kode SKU harus unik, tidak boleh sama. Silakan gunakan kode SKU yang berbeda.')
                    ->persistent()
                    ->send();
                
                $this->halt();
            }

            // Create gallery images
            foreach ($galleryImagesData as $index => $imageData) {
                if (!empty($imageData['image_path'])) {
                    $product->images()->create([
                        'image_path' => $imageData['image_path'],
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        $this->redirect($this->getResource()::getUrl('edit', ['record' => $product->id]));

        return $product;
    }
}
