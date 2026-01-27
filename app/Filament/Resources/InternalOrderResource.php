<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternalOrderResource\Pages;
use App\Models\Order;
use App\Models\Sku;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InternalOrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $slug = 'internal-orders';

    protected static string|\UnitEnum|null $navigationGroup = 'Internal Management';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Order Manual';

    protected static ?string $modelLabel = 'Order Manual';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole(['admin', 'finance']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        \Filament\Schemas\Components\Section::make()
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Placeholder::make('order_number_display')
                                            ->label('Order Number')
                                            ->content(function (Get $get, Set $set) {
                                                $state = $get('order_number');
                                                if (! $state) {
                                                    do {
                                                        $prefix = global_config('prefix_trx') ?? 'ORD-';
                                                        $orderNumber = $prefix.strtoupper(substr(uniqid(), -6));
                                                    } while (Order::where('order_number', $orderNumber)->exists());
                                                    $set('order_number', $orderNumber);

                                                    return $orderNumber;
                                                }

                                                return $state;
                                            }),
                                        Forms\Components\Hidden::make('order_number'),

                                        Forms\Components\Placeholder::make('created_at_display')
                                            ->label('Date')
                                            ->content(now()->toDayDateTimeString()),
                                        Forms\Components\Hidden::make('created_at')
                                            ->default(now()),

                                        Forms\Components\Placeholder::make('session_id_display')
                                            ->label('Session ID')
                                            ->content(function (Get $get, Set $set) {
                                                $state = $get('session_id');
                                                if (! $state) {
                                                    $uuid = (string) Str::uuid();
                                                    $set('session_id', $uuid);

                                                    return $uuid;
                                                }

                                                return $state;
                                            }),
                                        Forms\Components\Hidden::make('session_id'),
                                        Forms\Components\Placeholder::make('user_display')
                                            ->label('Operator')
                                            ->content(fn () => Auth::user()->name),
                                        Forms\Components\Hidden::make('user_id')
                                            ->default(fn () => Auth::id()),
                                    ]),
                            ]),

                        \Filament\Schemas\Components\Section::make('Customer')
                            ->schema([
                                Forms\Components\TextInput::make('customer_name')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('customer_email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('customer_phone')
                                    ->label('Phone')
                                    ->tel()
                                    ->required()
                                    ->maxLength(20),
                                Forms\Components\Hidden::make('payment_method')
                                    ->default('cash'),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'processing' => 'Processing',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('completed')
                                    ->required(),
                                Forms\Components\Select::make('payment_status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'paid' => 'Paid',
                                        'failed' => 'Failed',
                                    ])
                                    ->default('paid')
                                    ->required(),
                            ])->columns(2),

                        \Filament\Schemas\Components\Section::make('Order')
                            ->columnSpan('full')
                            ->schema([
                                Forms\Components\Repeater::make('items')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Select::make('sku_id')
                                            ->label('Product')
                                            ->columnSpanFull()
                                            ->searchable()
                                            ->getSearchResultsUsing(function (string $search) {
                                                return Sku::query()
                                                    ->join('products', 'skus.product_id', '=', 'products.id')
                                                    ->where(function ($query) use ($search) {
                                                        $query->where('skus.sku', 'like', "%{$search}%")
                                                            ->orWhere('products.name', 'like', "%{$search}%");
                                                    })
                                                    ->select('skus.id', 'skus.sku', 'products.name as product_name')
                                                    ->limit(50)
                                                    ->get()
                                                    ->mapWithKeys(fn ($sku) => [$sku->id => "{$sku->product_name} ({$sku->sku})"]);
                                            })
                                            ->getOptionLabelUsing(function ($value) {
                                                $sku = Sku::join('products', 'skus.product_id', '=', 'products.id')
                                                    ->where('skus.id', $value)
                                                    ->select('skus.sku', 'products.name as product_name')
                                                    ->first();

                                                return $sku ? "{$sku->product_name} ({$sku->sku})" : null;
                                            })
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                if ($sku = Sku::with('product')->find($state)) {
                                                    $price = (int) ($sku->selling_price ?? 0);
                                                    $set('price', $price);
                                                    $set('product_name', $sku->product->name);
                                                    $set('sku_code', $sku->sku);
                                                    $set('product_id', $sku->product_id);
                                                    $set('weight', $sku->weight ?? 1);

                                                    $quantity = (int) $get('quantity') ?: 1;
                                                    $set('subtotal', $price * $quantity);
                                                }
                                            }),
                                        Forms\Components\Hidden::make('product_id'),
                                        Forms\Components\Hidden::make('product_name'),
                                        Forms\Components\Hidden::make('sku_code'),
                                        Forms\Components\Hidden::make('weight')->default(1),

                                        Forms\Components\TextInput::make('quantity')
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(1)
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => $set('subtotal', $state * $get('price'))),

                                        Forms\Components\TextInput::make('price')
                                            ->label('Price')
                                            ->numeric()
                                            ->readOnly()
                                            ->prefix('Rp'),

                                        Forms\Components\TextInput::make('subtotal')
                                            ->numeric()
                                            ->readOnly()
                                            ->prefix('Rp')
                                            ->dehydrated()
                                            ->default(0),
                                    ])
                                    ->columns(3)
                                    ->live()
                                    ->deleteAction(
                                        fn ($action) => $action
                                            ->requiresConfirmation()
                                            ->after(function (Get $get, Set $set) {
                                                $items = collect($get('items'));
                                                $subtotal = $items->sum(fn ($item) => (int) ($item['quantity'] ?? 0) * (int) ($item['price'] ?? 0));
                                                $set('subtotal', $subtotal);
                                                $shipping = (int) $get('shipping_cost');
                                                $set('total', $subtotal + $shipping);
                                            }),
                                    )
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $items = collect($get('items'));
                                        $subtotal = $items->sum(fn ($item) => (int) ($item['quantity'] ?? 0) * (int) ($item['price'] ?? 0));
                                        $set('subtotal', $subtotal);
                                        $shipping = (int) $get('shipping_cost');
                                        $set('total', $subtotal + $shipping);
                                    }),
                            ]),
                    ])->columnSpan(['lg' => 2]),

                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        \Filament\Schemas\Components\Section::make('Shipping Details')
                            ->schema([
                                Forms\Components\Textarea::make('shipping_address')
                                    ->label('Address')
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('shipping_province_id')
                                    ->label('Province')
                                    ->options(fn () => collect((new \App\Services\RajaongkirService)->getProvinces())->pluck('name', 'id'))
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('shipping_city_id', null);
                                        $set('shipping_district_id', null);
                                        $set('shipping_subdistrict_id', null);
                                        $set('shipping_courier', null);
                                        $set('shipping_ref', null);
                                        $set('shipping_cost', 0);
                                        $set('shipping_method', null);
                                        $set('shipping_service', null);
                                        $province = collect((new \App\Services\RajaongkirService)->getProvinces())->firstWhere('id', $state);
                                        $set('shipping_province', $province['name'] ?? null);
                                    })
                                    ->dehydrated(false),
                                Forms\Components\Hidden::make('shipping_province'),

                                Forms\Components\Select::make('shipping_city_id')
                                    ->label('City')
                                    ->options(fn (Get $get) => $get('shipping_province_id') ? collect((new \App\Services\RajaongkirService)->getCities($get('shipping_province_id')))->mapWithKeys(fn ($item) => [$item['id'] => ($item['type'] ?? '').' '.($item['name'] ?? '')]) : [])
                                    ->searchable()
                                    ->live()
                                    ->preload()
                                    ->disabled(fn (Get $get) => ! $get('shipping_province_id'))
                                    ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                        $set('shipping_district_id', null);
                                        $set('shipping_subdistrict_id', null);
                                        $set('shipping_courier', null);
                                        $set('shipping_ref', null);
                                        $set('shipping_cost', 0);
                                        $set('shipping_method', null);
                                        $set('shipping_service', null);
                                        $city = collect((new \App\Services\RajaongkirService)->getCities($get('shipping_province_id')))->firstWhere('id', $state);
                                        $set('shipping_city', isset($city) ? (($city['type'] ?? '').' '.($city['name'] ?? '')) : null);
                                    })
                                    ->dehydrated(false),
                                Forms\Components\Hidden::make('shipping_city'),

                                Forms\Components\Select::make('shipping_district_id')
                                    ->label('District')
                                    ->options(fn (Get $get) => $get('shipping_city_id') ? collect((new \App\Services\RajaongkirService)->getDistricts($get('shipping_city_id')))->pluck('name', 'id') : [])
                                    ->searchable()
                                    ->live()
                                    ->preload()
                                    ->disabled(fn (Get $get) => ! $get('shipping_city_id'))
                                    ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                        $set('shipping_subdistrict_id', null);
                                        $set('shipping_courier', null);
                                        $set('shipping_ref', null);
                                        $set('shipping_cost', 0);
                                        $set('shipping_method', null);
                                        $set('shipping_service', null);
                                        $district = collect((new \App\Services\RajaongkirService)->getDistricts($get('shipping_city_id')))->firstWhere('id', $state);
                                        $set('shipping_district', $district['name'] ?? null);
                                    })
                                    ->dehydrated(false),
                                Forms\Components\Hidden::make('shipping_district'),

                                Forms\Components\Select::make('shipping_subdistrict_id')
                                    ->label('Subdistrict')
                                    ->options(fn (Get $get) => $get('shipping_district_id') ? collect((new \App\Services\RajaongkirService)->getSubdistricts($get('shipping_district_id')))->pluck('name', 'id') : [])
                                    ->searchable()
                                    ->live()
                                    ->preload()
                                    ->disabled(fn (Get $get) => ! $get('shipping_district_id'))
                                    ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                        $set('shipping_courier', null);
                                        $set('shipping_ref', null);
                                        $set('shipping_cost', 0);
                                        $set('shipping_method', null);
                                        $set('shipping_service', null);
                                        $subdistrict = collect((new \App\Services\RajaongkirService)->getSubdistricts($get('shipping_district_id')))->firstWhere('id', $state);
                                        $set('shipping_subdistrict', $subdistrict['name'] ?? null);
                                    })
                                    ->dehydrated(false),
                                Forms\Components\Hidden::make('shipping_subdistrict'),

                                Forms\Components\TextInput::make('shipping_postal_code')
                                    ->label('Postal Code')
                                    ->numeric()
                                    ->required(),
                            ])->columns(1),

                        \Filament\Schemas\Components\Section::make('Shipping Service')
                            ->schema([
                                Forms\Components\Select::make('shipping_courier')
                                    ->options(['jne' => 'JNE', 'jnt' => 'J&T'])
                                    ->label('Courier')
                                    ->required()
                                    ->placeholder('Select Courier')
                                    ->disabled(fn (Get $get) => ! $get('shipping_subdistrict_id'))
                                    ->live()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function (Set $set, Get $get, $record) {
                                        if ($record && $record->shipping_method) {
                                            $parts = explode('_', $record->shipping_method);
                                            $set('shipping_courier', $parts[0] ?? null);
                                        }
                                    })
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('shipping_ref', null);
                                        $set('shipping_cost', 0);
                                        $set('shipping_method', null);
                                        $set('shipping_service', null);
                                    }),

                                Forms\Components\Select::make('shipping_ref')
                                    ->label('Service')
                                    ->required()
                                    ->placeholder('Select Service')
                                    ->disabled(fn (Get $get) => ! $get('shipping_courier'))
                                    ->options(function (Get $get, $record) {
                                        $originBranch = \App\Models\Branch::where('is_active', true)->orderBy('priority')->first();
                                        if (! $originBranch || ! $get('shipping_district_id') || ! $get('shipping_courier')) {
                                            return [];
                                        }

                                        $totalWeight = collect($get('items'))->sum(fn ($item) => (int) ($item['quantity'] ?? 0) * (int) ($item['weight'] ?? 1000));
                                        $useSubdistrict = (bool) $get('shipping_subdistrict_id');

                                        $params = [
                                            'origin' => $originBranch->subdistrict_id,
                                            'destination' => $useSubdistrict ? $get('shipping_subdistrict_id') : $get('shipping_district_id'),
                                            'weight' => max(200, $totalWeight),
                                            'courier' => $get('shipping_courier'),
                                            'price' => 'lowest',
                                            '_use_subdistrict' => $useSubdistrict,
                                        ];

                                        $results = (new \App\Services\RajaongkirService)->calculateDomesticCost($params);

                                        $options = [];
                                        foreach ($results as $result) {
                                            $code = strtolower($result['code']);
                                            $service = $result['service'];
                                            $cost = (int) $result['cost'];
                                            $etd = $result['etd'];
                                            $key = "{$code}_{$service}|{$cost}|{$service}";
                                            $label = strtoupper($code).' '.$service.' - Rp '.number_format($cost, 0, ',', '.')." ({$etd})";
                                            $options[$key] = $label;
                                        }

                                        return $options;
                                    })
                                    ->afterStateHydrated(function (Set $set, Get $get, $record) {
                                        if ($record && $record->shipping_method && $record->shipping_cost && $record->shipping_service) {
                                            $key = "{$record->shipping_method}|{$record->shipping_cost}|{$record->shipping_service}";
                                            $set('shipping_ref', $key);
                                        }
                                    })
                                    ->live()
                                    ->dehydrated(false)
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $cost = 0;
                                        if ($state) {
                                            $parts = explode('|', $state);
                                            $set('shipping_method', $parts[0] ?? null);
                                            $cost = (int) ($parts[1] ?? 0);
                                            $set('shipping_service', $parts[2] ?? null);
                                        } else {
                                            $set('shipping_method', null);
                                            $set('shipping_service', null);
                                        }
                                        $set('shipping_cost', $cost);
                                        $subtotal = collect($get('items'))->sum(fn ($item) => (int) ($item['quantity'] ?? 0) * (int) ($item['price'] ?? 0));
                                        $set('total', $subtotal + $cost);
                                    }),

                                Forms\Components\Hidden::make('shipping_method'),
                                Forms\Components\Hidden::make('shipping_service'),

                                Forms\Components\TextInput::make('shipping_cost')
                                    ->label('Cost')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->default(0),
                            ])->columns(1),

                        \Filament\Schemas\Components\Section::make('Summary')
                            ->extraAttributes([
                                'style' => 'background-color: rgba(var(--primary-600), 0.1); border: 1px solid rgba(var(--primary-600), 0.2);',
                                'class' => 'rounded-xl',
                            ])
                            ->schema([
                                Forms\Components\Placeholder::make('grand_total_display')
                                    ->label('Total Items')
                                    ->content(fn (Get $get) => 'Rp '.number_format($get('subtotal') ?? 0, 0, ',', '.'))
                                    ->extraAttributes(['class' => 'text-xl font-bold']),
                                Forms\Components\Hidden::make('total')
                                    ->default(0),
                                Forms\Components\Hidden::make('subtotal')
                                    ->default(0),
                                Forms\Components\Hidden::make('shipping_cost')
                                    ->default(0),
                                Forms\Components\Placeholder::make('shipping_cost_display')
                                    ->label('Shipping Cost')
                                    ->content(fn (Get $get) => 'Rp '.number_format($get('shipping_cost') ?? 0, 0, ',', '.'))
                                    ->extraAttributes(['class' => 'text-lg font-semibold']),
                                Forms\Components\Placeholder::make('final_total_display')
                                    ->label('Grand Total')
                                    ->content(function (Get $get) {
                                        $subtotal = (int) ($get('subtotal') ?? 0);
                                        $shipping = (int) ($get('shipping_cost') ?? 0);

                                        return 'Rp '.number_format($subtotal + $shipping, 0, ',', '.');
                                    })
                                    ->extraAttributes(['class' => 'text-2xl font-bold text-primary-600']),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->formatStateUsing(fn (string $state): string => 'Rp '.number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('shipping_city')
                    ->label('City'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Operator')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('payment_method', 'cash')
            ->whereHas('user');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInternalOrders::route('/'),
            'create' => Pages\CreateInternalOrder::route('/create'),
            'edit' => Pages\EditInternalOrder::route('/{record}/edit'),
        ];
    }
}
