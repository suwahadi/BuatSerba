<?php

namespace App\Filament\Resources\Payments;

use App\Filament\Resources\Payments\Pages\ManagePayments;
use App\Models\Payment;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = 'Pembayaran';

    protected static UnitEnum|string|null $navigationGroup = null;

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Pembayaran';

    protected static ?string $pluralModelLabel = 'Pembayaran';

    public static function canAccess(): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('resource.payments.view_any') || Auth::user()->hasRole('admin'));
    }

    public static function canCreate(): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('resource.payments.create') || Auth::user()->hasRole('admin'));
    }

    public static function canEdit($record): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('resource.payments.update') || Auth::user()->hasRole('admin'));
    }

    public static function canDelete($record): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('resource.payments.delete') || Auth::user()->hasRole('admin'));
    }

    public static function canDeleteAny(): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('resource.payments.delete') || Auth::user()->hasRole('admin'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Transaction Information')
                            ->schema([
                                Select::make('order_id')
                                    ->label('Order')
                                    ->relationship('order', 'order_number')
                                    ->searchable()
                                    ->preload()
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('transaction_id')
                                    ->label('Transaction ID')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('payment_gateway')
                                    ->label('Payment Gateway')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('payment_type')
                                    ->label('Payment Type')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('payment_channel')
                                    ->label('Payment Channel')
                                    ->disabled()
                                    ->dehydrated(false),

                                DateTimePicker::make('transaction_time')
                                    ->label('Transaction Time')
                                    ->disabled()
                                    ->dehydrated(false),
                            ])
                            ->columns(2),

                        Section::make('Payment Status')
                            ->schema([
                                TextInput::make('transaction_status')
                                    ->label('Transaction Status')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('fraud_status')
                                    ->label('Fraud Status')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('status_code')
                                    ->label('Status Code')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('status_message')
                                    ->label('Status Message')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3),

                        Section::make('API Response')
                            ->schema([
                                Textarea::make('midtrans_response')
                                    ->label('Response Data')
                                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
                                    ->rows(15)
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->collapsed(),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Timestamps')
                            ->schema([
                                Placeholder::make('paid_at')
                                    ->label('Paid At')
                                    ->content(fn ($record) => $record?->paid_at?->format('d M Y, H:i') ?? '-'),

                                Placeholder::make('expired_at')
                                    ->label('Expired At')
                                    ->content(fn ($record) => $record?->expired_at?->format('d M Y, H:i') ?? '-'),

                                Placeholder::make('created_at')
                                    ->label('Created At')
                                    ->content(fn ($record) => $record?->created_at?->format('d M Y, H:i') ?? '-'),

                                Placeholder::make('updated_at')
                                    ->label('Updated At')
                                    ->content(fn ($record) => $record?->updated_at?->format('d M Y, H:i') ?? '-'),
                            ]),

                        Section::make('Amount Information')
                            ->schema([
                                TextInput::make('gross_amount')
                                    ->label('Gross Amount')
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(false),
                            ])
                            ->columns(1),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.order_number')
                    ->label('Order Number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Order number copied!')
                    ->copyMessageDuration(1500),

                TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->limit(20),

                TextColumn::make('payment_gateway')
                    ->label('Gateway')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'midtrans' => 'info',
                        default => 'gray',
                    })
                    ->toggleable(),

                // TextColumn::make('payment_type')
                //     ->label('Type')
                //     ->searchable()
                //     ->sortable()
                //     ->toggleable(),

                TextColumn::make('payment_channel')
                    ->label('Channel')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('gross_amount')
                    ->label('Amount')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('transaction_status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'settlement', 'capture' => 'success',
                        'pending' => 'warning',
                        'deny', 'cancel', 'expire' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('fraud_status')
                    ->label('Fraud Status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'accept' => 'success',
                        'challenge' => 'warning',
                        'deny' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('transaction_time')
                    ->label('Transaction Time')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('transaction_status')
                    ->label('Transaction Status')
                    ->options([
                        'pending' => 'Pending',
                        'settlement' => 'Settlement',
                        'capture' => 'Capture',
                        'deny' => 'Deny',
                        'cancel' => 'Cancel',
                        'expire' => 'Expire',
                    ]),

                SelectFilter::make('payment_gateway')
                    ->label('Payment Gateway')
                    ->options([
                        'midtrans' => 'Midtrans',
                    ]),

                SelectFilter::make('fraud_status')
                    ->label('Fraud Status')
                    ->options([
                        'accept' => 'Accept',
                        'challenge' => 'Challenge',
                        'deny' => 'Deny',
                    ]),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make()
                    ->visible(fn ($record) => static::canAccess() && !static::canEdit($record)),
                \Filament\Actions\EditAction::make()
                    ->label('Lihat')
                    ->visible(fn ($record) => static::canEdit($record)),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePayments::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Payment::where('transaction_status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $pendingCount = Payment::where('transaction_status', 'pending')->count();

        return $pendingCount > 0 ? 'warning' : null;
    }
}
