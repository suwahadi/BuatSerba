<?php

namespace App\Filament\Imports;

use App\Models\Sku;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class SkuImporter extends Importer
{
    protected static ?string $model = Sku::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('product_id')
                ->label('Product ID')
                ->requiredMapping()
                ->rules(['required', 'exists:products,id'])
                ->example('1'),
            
            ImportColumn::make('sku')
                ->label('SKU Code')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->example('SKU-001'),
            
            ImportColumn::make('base_price')
                ->label('Base Price')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0'])
                ->example('100000'),
            
            ImportColumn::make('selling_price')
                ->label('Selling Price')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric', 'min:0'])
                ->example('150000'),
            
            ImportColumn::make('wholesale_price')
                ->label('Wholesale Price')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0'])
                ->example('120000'),
            
            ImportColumn::make('wholesale_min_quantity')
                ->label('Wholesale Min Quantity')
                ->numeric()
                ->integer()
                ->rules(['nullable', 'integer', 'min:1'])
                ->example('10'),
            
            ImportColumn::make('stock_quantity')
                ->label('Stock Quantity')
                ->requiredMapping()
                ->numeric()
                ->integer()
                ->rules(['required', 'integer', 'min:0'])
                ->example('100'),
            
            ImportColumn::make('weight')
                ->label('Weight (grams)')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0'])
                ->example('500'),
            
            ImportColumn::make('length')
                ->label('Length (cm)')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0'])
                ->example('10'),
            
            ImportColumn::make('width')
                ->label('Width (cm)')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0'])
                ->example('5'),
            
            ImportColumn::make('height')
                ->label('Height (cm)')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0'])
                ->example('2'),
            
            ImportColumn::make('is_active')
                ->label('Is Active')
                ->boolean()
                ->rules(['boolean'])
                ->example('1'),
        ];
    }

    public function resolveRecord(): ?Sku
    {
        // Try to find existing SKU by SKU code to update, or create new
        return Sku::firstOrNew([
            'sku' => $this->data['sku'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your sku import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
