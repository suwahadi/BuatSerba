<?php

namespace App\Filament\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sku;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class MasterProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            // ImportColumn::make('slug')
            //     ->rules(['max:255']),

            ImportColumn::make('category')
                ->requiredMapping()
                ->relationship(resolveUsing: 'name'),

            ImportColumn::make('description')
                ->rules(['nullable']),

            ImportColumn::make('main_image')
                ->label('Main Image')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('is_featured')
                ->boolean()
                ->rules(['boolean']),

            ImportColumn::make('is_active')
                ->label('Active')
                ->boolean()
                ->rules(['boolean']),

            ImportColumn::make('sku_code')
                ->label('SKU Code')
                ->rules(['nullable', 'max:255'])
                ->fillRecordUsing(fn () => null),

            ImportColumn::make('unit_cost')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0'])
                ->fillRecordUsing(fn () => null),

            ImportColumn::make('base_price')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0'])
                ->fillRecordUsing(fn () => null),

            ImportColumn::make('selling_price')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric', 'min:0'])
                ->fillRecordUsing(fn () => null),

            ImportColumn::make('stock_quantity')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0'])
                ->fillRecordUsing(fn () => null),

            ImportColumn::make('weight')
                ->label('Weight (g)')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0'])
                ->fillRecordUsing(fn () => null),

            ImportColumn::make('length')
                ->label('Length (cm)')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0'])
                ->fillRecordUsing(fn () => null),

            ImportColumn::make('width')
                ->label('Width (cm)')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0'])
                ->fillRecordUsing(fn () => null),

            ImportColumn::make('height')
                ->label('Height (cm)')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0'])
                ->fillRecordUsing(fn () => null),

            // ImportColumn::make('sku_is_active')
            //     ->label('SKU Active')
            //     ->boolean()
            //     ->rules(['boolean'])
            //     ->fillRecordUsing(fn () => null),
        ];
    }

    public function resolveRecord(): ?Product
    {
        $categoryName = $this->data['category'] ?? null;

        if (! $categoryName) {
            return null;
        }

        // Find or create category
        $category = Category::firstOrCreate(
            ['name' => $categoryName],
            ['slug' => Str::slug($categoryName)]
        );

        // Create product
        $product = Product::create([
            'name' => $this->data['name'],
            'slug' => $this->data['slug'] ?? Str::slug($this->data['name']),
            'category_id' => $category->id,
            'description' => $this->data['description'] ?? null,
            'main_image' => $this->data['main_image'] ?? null,
            'is_featured' => $this->data['is_featured'] ?? false,
            'is_active' => $this->data['is_active'] ?? true,
        ]);

        // Create SKU
        Sku::create([
            'product_id' => $product->id,
            'sku' => $this->data['sku_code'] ?? 'SKU-'.strtoupper(Str::random(8)),
            'unit_cost' => $this->data['unit_cost'] ?? 0,
            'base_price' => $this->data['base_price'] ?? 0,
            'selling_price' => $this->data['selling_price'],
            'stock_quantity' => $this->data['stock_quantity'] ?? 0,
            'weight' => $this->data['weight'] ?? null,
            'length' => $this->data['length'] ?? null,
            'width' => $this->data['width'] ?? null,
            'height' => $this->data['height'] ?? null,
            'is_active' => $this->data['sku_is_active'] ?? true,
        ]);

        $product->load('sku', 'category');

        return $product;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return '';
    }
}
