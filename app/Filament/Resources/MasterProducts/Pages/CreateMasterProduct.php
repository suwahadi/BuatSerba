<?php

namespace App\Filament\Resources\MasterProducts\Pages;

use App\Filament\Resources\MasterProducts\MasterProductResource;
use App\Models\Sku;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateMasterProduct extends CreateRecord
{
    protected static string $resource = MasterProductResource::class;

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Extract SKU data
        $skuData = $data['sku'] ?? [];
        unset($data['sku']);

        // Create product
        $product = static::getModel()::create($data);

        // Create SKU if product created successfully
        if ($product) {
            Sku::create([
                'product_id' => $product->id,
                'sku' => $skuData['sku'] ?? 'SKU-'.strtoupper(Str::random(8)),
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
        }

        return $product;
    }
}
