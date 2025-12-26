<?php

namespace App\Filament\Resources\MasterProducts\Pages;

use App\Filament\Resources\MasterProducts\MasterProductResource;
use App\Models\Sku;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditMasterProduct extends EditRecord
{
    protected static string $resource = MasterProductResource::class;

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
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

        $record->update($data);

        Sku::updateOrCreate(
            ['product_id' => $record->id],
            [
                'sku' => $skuData['sku'] ?? $record->sku?->sku ?? 'SKU-'.strtoupper(\Illuminate\Support\Str::random(8)),
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

        return $record;
    }
}
