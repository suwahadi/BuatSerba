<?php

namespace App\Filament\Resources\Stocks;

use App\Filament\Resources\Stocks\Pages\CreateStock;
use App\Filament\Resources\Stocks\Pages\EditStock;
use App\Filament\Resources\Stocks\Pages\ListStocks;
use App\Models\BranchInventory;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                                    ->columnSpan(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (callable $set, $state) {
                                        $set('sku_id', null);
                                    }),

                                Select::make('sku_id')
                                    ->label('Product SKU')
                                    ->relationship('sku', 'sku')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->product->name} - {$record->sku}")
                                    ->columnSpan(1)
                                    ->searchPrompt('Search by SKU code or product name')
                                    ->getSearchResultsUsing(function (string $search): array {
                                        return \App\Models\Sku::where('sku', 'like', "%{$search}%")
                                            ->orWhereHas('product', function ($query) use ($search) {
                                                $query->where('name', 'like', "%{$search}%");
                                            })
                                            ->limit(50)
                                            ->get()
                                            ->mapWithKeys(fn ($record) => [
                                                $record->id => "{$record->product->name} - {$record->sku}",
                                            ])
                                            ->toArray();
                                    })
                                    ->unique(
                                        table: 'branch_inventory',
                                        column: 'sku_id',
                                        ignoreRecord: true,
                                        modifyRuleUsing: function ($get, $rule) {
                                            return $rule->where('branch_id', $get('branch_id'));
                                        }
                                    )
                                    ->validationMessages([
                                        'unique' => 'SKU sudah ada untuk cabang ini. Silakan edit catatan stok yang sudah ada.',
                                    ]),

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

                // Group::make()
                //     ->schema([
                //         Section::make('Stock Levels')
                //             ->schema([
                //                 TextInput::make('minimum_stock_level')
                //                     ->label('Minimum Stock Level')
                //                     ->numeric()
                //                     ->default(0)
                //                     ->minValue(0)
                //                     ->helperText('Alert when stock falls below this level'),

                //                 TextInput::make('reorder_point')
                //                     ->label('Reorder Point')
                //                     ->numeric()
                //                     ->default(0)
                //                     ->minValue(0)
                //                     ->helperText('Trigger reorder when stock reaches this level'),
                //             ]),
                //     ])
                //     ->columnSpan(['lg' => 1]),
            ])
            ->columns(2);
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

                // SelectFilter::make('product')
                //     ->label('Product')
                //     ->relationship('sku.product', 'name')
                //     ->searchable()
                //     ->preload()
                //     ->getOptionLabelFromRecordUsing(function ($record) {
                //         $firstSku = $record->skus->first();
                //         $skuCode = $firstSku ? $firstSku->sku : 'N/A';
                //         return "{$record->name} ({$skuCode})";
                //     })
                //     ->modifyQueryUsing(function ($query, $search) {
                //         if ($search) {
                //             $query->where(function ($q) use ($search) {
                //                 $q->where('name', 'like', "%{$search}%")
                //                     ->orWhereHas('skus', function ($skuQuery) use ($search) {
                //                         $skuQuery->where('sku', 'like', "%{$search}%");
                //                     });
                //             });
                //         }
                //     }),

                // SelectFilter::make('stock_status')
                //     ->label('Stock Status')
                //     ->options([
                //         'low' => 'Low Stock',
                //         'reorder' => 'Needs Reorder',
                //         'ok' => 'Stock OK',
                //     ])
                //     ->query(function ($query, $state) {
                //         if ($state['value'] === 'low') {
                //             return $query->whereColumn('quantity_available', '<=', 'minimum_stock_level');
                //         }
                //         if ($state['value'] === 'reorder') {
                //             return $query->whereColumn('quantity_available', '<=', 'reorder_point')
                //                 ->whereColumn('quantity_available', '>', 'minimum_stock_level');
                //         }
                //         if ($state['value'] === 'ok') {
                //             return $query->whereColumn('quantity_available', '>', 'reorder_point');
                //         }
                //     }),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\ViewAction::make(),
            ])
            ->toolbarActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->label('Export Data')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->exports([
                        \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                            ->fromTable()
                            ->withFilename('stock-management-'.date('Y-m-d_His'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::CSV)
                            ->withColumns([
                                \pxlrbt\FilamentExcel\Columns\Column::make('branch.name')
                                    ->heading('Branch'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('sku.product.name')
                                    ->heading('Product'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('sku.sku')
                                    ->heading('SKU'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('quantity_available')
                                    ->heading('Available'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('quantity_reserved')
                                    ->heading('Reserved'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('total_stock')
                                    ->heading('Total Stock')
                                    ->getStateUsing(function ($record) {
                                        return $record->quantity_available + $record->quantity_reserved;
                                    }),
                                \pxlrbt\FilamentExcel\Columns\Column::make('updated_at')
                                    ->heading('Last Updated')
                                    ->formatStateUsing(fn ($state) => $state?->format('Y-m-d H:i:s')),
                            ]),
                    ]),
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

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $exists = BranchInventory::where('branch_id', $data['branch_id'])
            ->where('sku_id', $data['sku_id'])
            ->exists();

        if ($exists) {
            Notification::make()
                ->title('Duplicate Entry')
                ->body('This SKU already exists for the selected branch. Please edit the existing stock record instead.')
                ->danger()
                ->send();

            return [];
        }

        return $data;
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) BranchInventory::whereColumn('quantity_available', '<=', 'minimum_stock_level')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $lowStockCount = BranchInventory::whereColumn('quantity_available', '<=', 'minimum_stock_level')->count();

        return $lowStockCount > 0 ? 'danger' : 'success';
    }
}
