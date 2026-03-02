<?php

namespace App\Filament\Resources\MasterProducts\Pages;

use App\Filament\Resources\MasterProducts\MasterProductResource;
use App\Models\Sku;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;

class EditMasterProduct extends EditRecord
{
    protected static string $resource = MasterProductResource::class;

    protected function beforeSave(): void
    {
        // Validate variant SKUs before saving
        $variantsData = $this->data['variants'] ?? [];
        $currentVariantIds = $this->record->variantsForRepeater->pluck('id')->toArray();
        
        foreach ($variantsData as $index => $variant) {
            if (!empty($variant['sku'])) {
                $query = Sku::where('sku', $variant['sku']);
                
                // Ignore the current variant being edited if it has an ID
                if (!empty($variant['id']) && in_array($variant['id'], $currentVariantIds)) {
                    $query->where('id', '!=', $variant['id']);
                }
                
                if ($query->exists()) {
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

    protected function getHeaderActions(): array
    {
        return [
            //DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $sku = $this->record->sku;

        if ($sku) {
            $data['sku'] = [
                'sku' => $sku->sku,
                'unit_cost' => $sku->unit_cost,
                'base_price' => $sku->base_price,
                'selling_price' => $sku->selling_price,
                'stock_quantity' => $sku->stock_quantity,
                'weight' => $sku->weight,
                'length' => $sku->length,
                'width' => $sku->width,
                'height' => $sku->height,
                'attributes' => $sku->attributes,
                'is_active' => $sku->is_active,
            ];
        }

        // Load gallery images
        $galleryImages = $this->record->images()->orderBy('sort_order')->get();
        if ($galleryImages->isNotEmpty()) {
            $data['gallery_images'] = $galleryImages->map(function ($image) {
                return ['image_path' => $image->image_path];
            })->toArray();
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $skuFields = ['sku_code', 'base_price', 'selling_price', 'stock_quantity', 'weight', 'length', 'width', 'height', 'is_active'];

        foreach ($skuFields as $field) {
            unset($data[$field]);
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $skuData = $data['sku'] ?? [];
        unset($data['sku']);

        // Extract gallery images data
        $galleryImagesData = $data['gallery_images'] ?? [];
        unset($data['gallery_images']);

        $record->update($data);

        // Handle gallery images
        $this->syncGalleryImages($record, $galleryImagesData);

        try {
            Sku::updateOrCreate(
                ['product_id' => $record->id],
                [
                    'sku' => $skuData['sku'] ?? $record->sku?->sku ?? 'SKU-'.strtoupper(\Illuminate\Support\Str::random(8)),
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
                ]
            );
        } catch (UniqueConstraintViolationException $e) {
            Notification::make()
                ->danger()
                ->title('Error: SKU Duplikat')
                ->body('Maaf, Kode SKU harus unik, tidak boleh sama. Silakan gunakan kode SKU yang berbeda.')
                ->persistent()
                ->send();
            
            $this->halt();
        }

        $this->redirect($this->getResource()::getUrl('edit', ['record' => $record]));

        return $record;
    }

    protected function syncGalleryImages($product, array $galleryImagesData): void
    {
        // Delete all existing gallery images
        $product->images()->delete();

        // Create new gallery images
        foreach ($galleryImagesData as $index => $imageData) {
            if (!empty($imageData['image_path'])) {
                $product->images()->create([
                    'image_path' => $imageData['image_path'],
                    'sort_order' => $index,
                ]);
            }
        }
    }
}
