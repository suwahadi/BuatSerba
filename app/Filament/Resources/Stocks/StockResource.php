<?php

namespace App\Filament\Resources\Stocks;

use App\Filament\Resources\Stocks\Pages\CreateStock;
use App\Filament\Resources\Stocks\Pages\EditStock;
use App\Filament\Resources\Stocks\Pages\ListStocks;
use App\Models\BranchInventory;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockResource extends Resource
{
    protected static ?string $model = BranchInventory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $navigationLabel = 'Stock Management';

    protected static ?string $modelLabel = 'Stock';

    protected static ?string $pluralModelLabel = 'Stocks';

    protected static ?string $slug = 'stocks';

    protected static ?int $navigationSort = 8;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Stock Information')
                            ->schema([
                                Select::make('branch_id')
                                    ->label('Branch')
                                    ->relationship('branch', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(1),

                                Select::make('sku_id')
                                    ->label('Product SKU')
                                    ->relationship('sku', 'sku')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->product->name} - {$record->sku}")
                                    ->columnSpan(1),

                                TextInput::make('quantity_available')
                                    ->label('Available Quantity')
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->minValue(0)
                                    ->columnSpan(1),

                                TextInput::make('quantity_reserved')
                                    ->label('Reserved Quantity')
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->minValue(0)
                                    ->columnSpan(1),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Stock Levels')
                            ->schema([
                                TextInput::make('minimum_stock_level')
                                    ->label('Minimum Stock Level')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->helperText('Alert when stock falls below this level'),

                                TextInput::make('reorder_point')
                                    ->label('Reorder Point')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->helperText('Trigger reorder when stock reaches this level'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('branch.name')
                    ->label('Branch')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sku.product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('sku.sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('SKU copied!')
                    ->copyMessageDuration(1500),

                // TextColumn::make('sku.attributes')
                //     ->label('Variant')
                //     ->formatStateUsing(function ($state) {
                //         if (empty($state)) {
                //             return '-';
                //         }

                //         return collect($state)->map(fn ($value, $key) => "{$key}: {$value}")->join(', ');
                //     })
                //     ->limit(30)
                //     ->toggleable(),

                TextColumn::make('quantity_available')
                    ->label('Available')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state, $record) => $state <= $record->minimum_stock_level ? 'danger' : ($state <= $record->reorder_point ? 'warning' : 'success'))
                    ->badge(),

                TextColumn::make('quantity_reserved')
                    ->label('Reserved')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('total_stock')
                    ->label('Total Stock')
                    ->getStateUsing(fn ($record) => $record->quantity_available + $record->quantity_reserved)
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('minimum_stock_level')
                    ->label('Min Level')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('reorder_point')
                    ->label('Reorder Point')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('branch')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('product')
                    ->label('Product')
                    ->relationship('sku.product', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('stock_status')
                    ->label('Stock Status')
                    ->options([
                        'low' => 'Low Stock',
                        'reorder' => 'Needs Reorder',
                        'ok' => 'Stock OK',
                    ])
                    ->query(function ($query, $state) {
                        if ($state['value'] === 'low') {
                            return $query->whereColumn('quantity_available', '<=', 'minimum_stock_level');
                        }
                        if ($state['value'] === 'reorder') {
                            return $query->whereColumn('quantity_available', '<=', 'reorder_point')
                                ->whereColumn('quantity_available', '>', 'minimum_stock_level');
                        }
                        if ($state['value'] === 'ok') {
                            return $query->whereColumn('quantity_available', '>', 'reorder_point');
                        }
                    }),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\ViewAction::make(),
            ])
            ->toolbarActions([
                // Bulk actions can be added here if needed
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStocks::route('/'),
            'create' => CreateStock::route('/create'),
            'edit' => EditStock::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function getNavigationBadge(): ?string
    {
        // Show count of low stock items
        return (string) BranchInventory::whereColumn('quantity_available', '<=', 'minimum_stock_level')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $lowStockCount = BranchInventory::whereColumn('quantity_available', '<=', 'minimum_stock_level')->count();

        return $lowStockCount > 0 ? 'danger' : 'success';
    }
}
