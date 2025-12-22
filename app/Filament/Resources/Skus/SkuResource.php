<?php

namespace App\Filament\Resources\Skus;

use App\Filament\Resources\Skus\Pages\CreateSku;
use App\Filament\Resources\Skus\Pages\EditSku;
use App\Filament\Resources\Skus\Pages\ListSkus;
use App\Filament\Resources\Skus\Schemas\SkuForm;
use App\Filament\Resources\Skus\Tables\SkusTable;
use App\Models\Sku;
use BackedEnum;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SkuResource extends Resource
{
    protected static ?string $model = Sku::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $recordTitleAttribute = 'sku';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Information')
                    ->description('Select the product and define the SKU code')
                    ->schema([
                        Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('sku')
                            ->label('SKU Code')
                            ->required()
                            ->unique(Sku::class, 'sku', ignoreRecord: true)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Pricing & Stock')
                    ->description('Set pricing and stock quantity')
                    ->schema([
                        TextInput::make('base_price')
                            ->label('Base Price')
                            ->numeric()
                            ->prefix('Rp')
                            ->visible(fn ($livewire) => $livewire instanceof CreateRecord),
                        TextInput::make('selling_price')
                            ->label('Selling Price')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        TextInput::make('stock_quantity')
                            ->label('Stock Quantity')
                            ->numeric()
                            ->required()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Dimensions & Weight')
                    ->description('Product physical specifications')
                    ->schema([
                        TextInput::make('weight')
                            ->label('Weight (gram)')
                            ->numeric()
                            ->suffix('g')
                            ->placeholder('0'),
                        TextInput::make('length')
                            ->label('Length (cm)')
                            ->numeric()
                            ->suffix('cm')
                            ->placeholder('0'),
                        TextInput::make('width')
                            ->label('Width (cm)')
                            ->numeric()
                            ->suffix('cm')
                            ->placeholder('0'),
                        TextInput::make('height')
                            ->label('Height (cm)')
                            ->numeric()
                            ->suffix('cm')
                            ->placeholder('0'),
                    ])
                    ->columns(4)
                    ->collapsible(),

                Section::make('Attributes')
                    ->description('Additional product attributes (e.g., Color, Size, etc.)')
                    ->schema([
                        KeyValue::make('attributes')
                            ->label('Product Attributes')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stock_quantity')
                    ->sortable(),
                TextColumn::make('selling_price')
                    ->money('IDR')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->exports([
                        \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                            ->fromTable()
                            ->withFilename('skus-' . date('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                            ->withColumns([
                                \pxlrbt\FilamentExcel\Columns\Column::make('id'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('product_id')
                                    ->heading('Product ID'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('product.name')
                                    ->heading('Product Name'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('sku')
                                    ->heading('SKU Code'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('base_price')
                                    ->heading('Base Price'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('selling_price')
                                    ->heading('Selling Price'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('wholesale_price')
                                    ->heading('Wholesale Price'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('wholesale_min_quantity')
                                    ->heading('Wholesale Min Qty'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('stock_quantity')
                                    ->heading('Stock Quantity'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('weight')
                                    ->heading('Weight (g)'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('length')
                                    ->heading('Length (cm)'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('width')
                                    ->heading('Width (cm)'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('height')
                                    ->heading('Height (cm)'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('is_active')
                                    ->heading('Is Active')
                                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('created_at')
                                    ->heading('Created At'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('updated_at')
                                    ->heading('Updated At'),
                            ]),
                    ]),
                
                \Filament\Actions\ImportAction::make()
                    ->label('Import from Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('info')
                    ->importer(\App\Filament\Imports\SkuImporter::class),
            ])
            ->toolbarActions([
                // No bulk actions
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSkus::route('/'),
            'create' => CreateSku::route('/create'),
            'edit' => EditSku::route('/{record}/edit'),
        ];
    }
}
