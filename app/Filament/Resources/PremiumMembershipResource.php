<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PremiumMembershipResource\Pages;
use App\Models\PremiumMembership;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;

class PremiumMembershipResource extends Resource
{
    protected static ?string $model = PremiumMembership::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Premium Membership';

    protected static ?string $modelLabel = 'Premium Membership';

    protected static ?string $pluralModelLabel = 'Premium Memberships';

    protected static UnitEnum|string|null $navigationGroup = 'Membership';

    protected static ?int $navigationSort = 10;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Member Information')
                    ->schema([
                        TextInput::make('user.name')
                            ->label('Member Name')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('user.email')
                            ->label('Email')
                            ->email()
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('user.phone')
                            ->label('Phone')
                            ->tel()
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columnSpan(['lg' => 1]),

                Section::make('Membership Timeline')
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Created At')
                            ->content(fn (PremiumMembership $record): string => $record->created_at?->format('d M Y H:i:s') ?? '-'),

                        DateTimePicker::make('started_at')
                            ->label('Activated At')
                            ->visible(fn ($get) => $get('status') === 'active'),

                        DateTimePicker::make('expires_at')
                            ->label('Expires At')
                            ->visible(fn ($get) => $get('status') === 'active'),
                    ])
                    ->columnSpan(['lg' => 1]),

                Section::make('Payment Details')
                    ->schema([
                        TextInput::make('price')
                            ->label('Payment Amount (Rp)')
                            ->numeric()
                            ->disabled()
                            ->prefix('Rp '),

                        TextInput::make('payment_method')
                            ->disabled(),

                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'active' => 'Active',
                                'expired' => 'Expired',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),

                        FileUpload::make('payment_proof_path')
                            ->label('Payment Proof')
                            ->disk('public')
                            ->disabled()
                            ->dehydrated(false)
                            ->downloadable(),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Member')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Amount')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'pending' => 'warning',
                        'active' => 'success',
                        'expired' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('started_at')
                    ->label('Started')
                    ->dateTime('d M Y')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('d M Y')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPremiumMemberships::route('/'),
            'create' => Pages\CreatePremiumMembership::route('/create'),
            'edit' => Pages\EditPremiumMembership::route('/{record}/edit'),
        ];
    }
}
