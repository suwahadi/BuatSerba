<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Models\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        \Filament\Schemas\Components\Section::make('Customer Information')
                            ->schema([
                                \Filament\Forms\Components\Select::make('user_id')
                                    ->label('Registered User (Optional)')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload(), // Not required anymore
                                \Filament\Forms\Components\TextInput::make('customer_name')
                                    ->label('Customer Name (Manual)')
                                    ->required() // Required for guest orders
                                    ->maxLength(255),
                                \Filament\Forms\Components\TextInput::make('customer_email')
                                    ->email()
                                    ->maxLength(255),
                                \Filament\Forms\Components\TextInput::make('customer_phone')
                                    ->maxLength(20),
                            ])
                            ->columns(2),

                        \Filament\Schemas\Components\Section::make('Order Items')
                            ->schema([
                                \Filament\Forms\Components\Repeater::make('items')
                                    ->relationship('items')
                                    ->schema([
                                        \Filament\Forms\Components\TextInput::make('sku_code')
                                            ->label('SKU')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->inlineLabel(false),
                                        \Filament\Forms\Components\TextInput::make('quantity')
                                            ->label('Qty')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->inlineLabel(false),
                                        \Filament\Forms\Components\TextInput::make('price')
                                            ->label('Price')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->prefix('Rp')
                                            ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                                            ->inlineLabel(false),
                                        \Filament\Forms\Components\TextInput::make('subtotal')
                                            ->label('Subtotal')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->prefix('Rp')
                                            ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                                            ->inlineLabel(false),
                                    ])
                                    ->columns(4)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->defaultItems(0)
                                    ->itemLabel(fn (array $state): ?string => null)
                                    ->columnSpanFull(),
                                \Filament\Forms\Components\Placeholder::make('grand_total_display')
                                    ->label('Grand Total')
                                    ->content(function ($record) {
                                        if (! $record) {
                                            return 'Rp 0';
                                        }
                                        $itemsTotal = $record->items->sum('subtotal');

                                        return 'Rp '.number_format($itemsTotal, 0, ',', '.');
                                    })
                                    ->extraAttributes(['class' => 'text-lg font-bold'])
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->visible(fn ($record) => $record !== null && $record->items()->count() > 0),

                        \Filament\Schemas\Components\Section::make('Shipping Details')
                            ->schema([
                                \Filament\Forms\Components\Textarea::make('shipping_address')
                                    ->columnSpanFull(),
                                \Filament\Schemas\Components\Grid::make(2) // Forms Grid
                                    ->schema([
                                        \Filament\Forms\Components\TextInput::make('shipping_province'),
                                        \Filament\Forms\Components\TextInput::make('shipping_city'),
                                        \Filament\Forms\Components\TextInput::make('shipping_district'),
                                        \Filament\Forms\Components\TextInput::make('shipping_postal_code'),
                                    ]),
                                \Filament\Schemas\Components\Section::make('Shipping Service')
                                    ->schema([
                                        \Filament\Forms\Components\TextInput::make('shipping_method')
                                            ->label('Method (e.g. JNE)'),
                                        \Filament\Forms\Components\TextInput::make('shipping_service')
                                            ->label('Service (e.g. REG)'),
                                        \Filament\Forms\Components\TextInput::make('shipping_cost')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0),
                                    ])->columns(3),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        \Filament\Schemas\Components\Section::make('Order Details')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('order_number')
                                    ->required()
                                    ->default('ORD-'.random_int(100000, 999999)),
                                \Filament\Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'processing' => 'Processing',
                                        'shipped' => 'Shipped',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('pending')
                                    ->required(),
                                \Filament\Forms\Components\TextInput::make('total')
                                    ->label('Grand Total')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0),
                            ]),

                        \Filament\Schemas\Components\Section::make('Payment')
                            ->schema([
                                \Filament\Forms\Components\Select::make('payment_method')
                                    ->options([
                                        'bri' => 'BRI',
                                        'bni' => 'BNI',
                                        'bca' => 'BCA',
                                        'permata' => 'Permata',
                                    ]),
                                \Filament\Forms\Components\Select::make('payment_status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'paid' => 'Paid',
                                        'failed' => 'Failed',
                                    ])
                                    ->default('pending')
                                    ->required(),
                            ]),

                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label('Order Notes')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('user.name')
                    ->label('User Account')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('total')
                    ->money('IDR')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'info',
                        'shipped' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                \Filament\Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
                \Filament\Tables\Columns\TextColumn::make('shipping_city')
                    ->label('City')
                    ->toggleable(),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                // No bulk
            ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

}
