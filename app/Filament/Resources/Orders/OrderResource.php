<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Models\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Order';

    protected static UnitEnum|string|null $navigationGroup = null;

    protected static ?string $recordTitleAttribute = 'order_number';

    protected static ?string $modelLabel = 'Order';

    protected static ?string $pluralModelLabel = 'Order';

    public static function canAccess(): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('resource.orders.view_any') || Auth::user()->hasRole('admin'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        \Filament\Schemas\Components\Section::make('Customer Information')
                            ->schema([
                                \Filament\Forms\Components\Select::make('user_id')
                                    ->label('User (Optional)')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload(),
                                \Filament\Forms\Components\TextInput::make('customer_name')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(255),
                                \Filament\Forms\Components\TextInput::make('customer_email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255),
                                \Filament\Forms\Components\TextInput::make('customer_phone')
                                    ->label('Phone')
                                    ->maxLength(20),
                            ])
                            ->columns(2),

                        \Filament\Schemas\Components\Section::make('Order Items')
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('items_table')
                                    ->label('Items')
                                    ->hiddenLabel()
                                    ->content(function ($record) {
                                        if (! $record) {
                                            return '-';
                                        }

                                        $items = $record->items;
                                        $rows = '';

                                        foreach ($items as $item) {
                                            $productName = $item->product->name ?? $item->product_name ?? '-';
                                            $categoryName = $item->product->category->name ?? '-';
                                            $qty = $item->quantity;
                                            $price = number_format($item->price, 0, ',', '.');
                                            $subtotal = number_format($item->subtotal, 0, ',', '.');
                                            $sku = $item->sku_code ?? '-';

                                            $rows .= "
                                                <tr>
                                                    <td>{$sku}</td>
                                                    <td>
                                                        <span class='product-name'>{$productName}</span><br>
                                                        <small class='category-name'>{$categoryName}</small>
                                                    </td>
                                                    <td>{$qty}</td>
                                                    <td>Rp {$price}</td>
                                                    <td>Rp {$subtotal}</td>
                                                </tr>
                                            ";
                                        }

                                        $subtotal = $record->subtotal ?: $items->sum('subtotal');
                                        $shippingCost = $record->shipping_cost;
                                        $serviceFee = $record->service_fee;
                                        $discount = $record->discount;
                                        $grandTotal = $record->total;

                                        $fSubtotal = number_format($subtotal, 0, ',', '.');
                                        $fShipping = number_format($shippingCost, 0, ',', '.');
                                        $fServiceFee = number_format($serviceFee, 0, ',', '.');
                                        $fDiscount = number_format($discount, 0, ',', '.');
                                        $fGrandTotal = number_format($grandTotal, 0, ',', '.');

                                        $rows_summary = "
                                            <tr>
                                                <td colspan='4' style='text-align: right;'>Sub Total</td>
                                                <td>Rp {$fSubtotal}</td>
                                            </tr>
                                            <tr>
                                                <td colspan='4' style='text-align: right;'>Biaya Layanan</td>
                                                <td>Rp {$fServiceFee}</td>
                                            </tr>
                                            <tr>
                                                <td colspan='4' style='text-align: right;'>Ongkos Kirim</td>
                                                <td>Rp {$fShipping}</td>
                                            </tr>
                                        ";

                                        if ($discount > 0) {
                                            $rows_summary .= "
                                                <tr>
                                                    <td colspan='4' style='padding: 8px 24px; text-align: right; color: #dc2626;'>Diskon / Voucher</td>
                                                    <td style='padding: 8px 24px; color: #dc2626;'>- Rp {$fDiscount}</td>
                                                </tr>
                                            ";
                                        }

                                        return new \Illuminate\Support\HtmlString("
                                            <style>
                                                .order-items-table {
                                                    border: 1px solid #374151;
                                                    border-radius: 8px;
                                                    overflow: hidden;
                                                    overflow-x: auto;
                                                    background-color: #1f2937;
                                                }
                                                .order-items-table th {
                                                    background-color: #374151;
                                                    border-bottom: 1px solid #4b5563;
                                                    color: #f9fafb;
                                                    font-weight: 600;
                                                    padding: 12px 24px;
                                                    text-align: left;
                                                    border-right: 1px solid #4b5563;
                                                }
                                                .order-items-table td {
                                                    padding: 12px 24px;
                                                    color: #d1d5db;
                                                    border-right: 1px solid #374151;
                                                }
                                                .order-items-table tfoot {
                                                    border-top: 2px solid #374151;
                                                }
                                                .order-items-table .grand-total-row {
                                                    background-color: #374151;
                                                    border-top: 1px solid #4b5563;
                                                }
                                                .order-items-table .grand-total-row td {
                                                    color: #f9fafb;
                                                    font-weight: 700;
                                                    font-size: 0.95rem;
                                                }
                                                .order-items-table .product-name {
                                                    color: #f9fafb;
                                                    font-weight: 500;
                                                }
                                                .order-items-table .category-name {
                                                    color: #9ca3af;
                                                    font-size: 0.875rem;
                                                }
                                            </style>
                                            <div class='order-items-table'>
                                                <table style='width: 100%; min-width: 600px; border-collapse: collapse; font-size: 0.875rem; text-align: left;'>
                                                    <thead>
                                                        <tr>
                                                            <th>SKU</th>
                                                            <th>Nama Produk</th>
                                                            <th>Qty</th>
                                                            <th>Price</th>
                                                            <th>Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {$rows}
                                                    </tbody>
                                                    <tfoot>
                                                        {$rows_summary}
                                                        <tr class='grand-total-row'>
                                                            <td colspan='4' style='text-align: right;'>Grand Total</td>
                                                            <td>Rp {$fGrandTotal}</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        ");
                                    })
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->visible(fn ($record) => $record !== null && $record->items()->count() > 0),

                        \Filament\Schemas\Components\Section::make('Shipping Details')
                            ->schema([
                                \Filament\Forms\Components\Textarea::make('shipping_address')
                                    ->columnSpanFull(),
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        \Filament\Forms\Components\TextInput::make('shipping_province'),
                                        \Filament\Forms\Components\TextInput::make('shipping_city'),
                                        \Filament\Forms\Components\TextInput::make('shipping_district'),
                                        \Filament\Forms\Components\TextInput::make('shipping_postal_code'),
                                    ]),
                            ]),
                        \Filament\Schemas\Components\Section::make('Order Notes')
                            ->schema([
                                \Filament\Forms\Components\Textarea::make('notes')
                                    ->label('Order Notes')
                                    ->columnSpanFull(),
                            ])
                            ->collapsible(),
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
                                        'delivered' => 'Delivered',
                                        'cancelled' => 'Cancelled',
                                        'expired' => 'Expired',
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
                                    ->label('Method')
                                    ->options([
                                        'bca' => 'BCA Virtual Account',
                                        'mandiri' => 'Mandiri Virtual Account',
                                        'bni' => 'BNI Virtual Account',
                                        'bri' => 'BRI Virtual Account',
                                        'qris' => 'QRIS',
                                        'transfer' => 'Bank Transfer',
                                        'cash' => 'Cash',
                                        'member_balance' => 'Member Balance',
                                    ]),
                                \Filament\Forms\Components\Select::make('payment_status')
                                    ->label('Status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'paid' => 'Paid',
                                        'failed' => 'Failed',
                                        'expired' => 'Expired',
                                    ])
                                    ->default('pending')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $record) {
                                        if ($state === 'paid' && $record) {
                                            \App\Events\OrderPaid::dispatch($record);
                                        }
                                    }),
                            ]),

                        \Filament\Schemas\Components\Section::make('Shipping')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('shipping_method')
                                    ->readOnly()
                                    ->label('Service'),
                                \Filament\Forms\Components\TextInput::make('shipping_cost')
                                    ->label('Cost')
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('Rp')
                                    ->default(0),
                            ])->columns(1),

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
                        'processing' => 'warning',
                        'shipped' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'delivered' => 'success',
                        'expired' => 'danger',
                        default => 'gray',
                    }),
                \Filament\Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                        'expired' => 'danger',
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
                        'expired' => 'Expired',
                    ]),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                // No bulk
            ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => \App\Filament\Resources\Orders\Pages\CreateOrder::route('/create'),
            'view' => \App\Filament\Resources\Orders\Pages\ViewOrder::route('/{record}'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return true;
    }
}
