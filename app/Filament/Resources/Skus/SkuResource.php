<?php

namespace App\Filament\Resources\Skus;

use App\Filament\Resources\Skus\Pages\CreateSku;
use App\Filament\Resources\Skus\Pages\EditSku;
use App\Filament\Resources\Skus\Pages\ListSkus;
use App\Filament\Resources\Skus\Schemas\SkuForm;
use App\Filament\Resources\Skus\Tables\SkusTable;
use App\Models\Sku;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
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
                \Filament\Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                \Filament\Forms\Components\TextInput::make('sku')
                    ->label('SKU Code')
                    ->required()
                    ->unique(Sku::class, 'sku', ignoreRecord: true),
                \Filament\Forms\Components\TextInput::make('stock_quantity')
                    ->numeric()
                    ->required()
                    ->default(0),
                \Filament\Forms\Components\TextInput::make('selling_price')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('base_price')
                    ->numeric()
                    ->prefix('Rp')
                    ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord), 
                \Filament\Forms\Components\KeyValue::make('attributes')
                    ->columnSpanFull(),
                \Filament\Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('sku')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('product.name')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('stock_quantity')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('selling_price')
                    ->money('IDR')
                    ->sortable(),
                \Filament\Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                \Filament\Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
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
