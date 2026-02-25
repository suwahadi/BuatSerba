<?php

namespace App\Filament\Resources\MemberWallets;

use App\Filament\Resources\MemberWallets\Pages\ViewMemberBalanceLedger;
use App\Models\MemberBalanceLedger;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class MemberBalanceLedgerResource extends Resource
{
    protected static ?string $model = MemberBalanceLedger::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Riwayat Saldo';

    protected static UnitEnum|string|null $navigationGroup = null;

    protected static ?string $modelLabel = 'Riwayat Saldo';

    protected static ?string $pluralModelLabel = 'Riwayat Saldo';

    protected static ?string $recordTitleAttribute = 'user.name';

    public static function canAccess(): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('resource.member_balance_ledgers.view_any') || Auth::user()->hasRole('admin'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Member')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('type')
                    ->label('Tipe')
                    ->required()
                    ->disabled(),
                TextInput::make('amount')
                    ->label('Jumlah')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->step(0.01)
                    ->disabled(),
                TextInput::make('balance_before')
                    ->label('Saldo Sebelum')
                    ->numeric()
                    ->prefix('Rp')
                    ->step(0.01)
                    ->disabled(),
                TextInput::make('balance_after')
                    ->label('Saldo Sesudah')
                    ->numeric()
                    ->prefix('Rp')
                    ->step(0.01)
                    ->disabled(),
                TextInput::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->disabled(),
                TextInput::make('reference_code')
                    ->label('Referensi')
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Member')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'credit' => 'success',
                        'debit' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn ($record) => $record->type === 'credit' ? 'success' : 'danger'),
                TextColumn::make('balance_before')
                    ->label('Saldo Sebelum')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('balance_after')
                    ->label('Saldo Sesudah')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('reference_code')
                    ->label('Referensi')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'credit' => 'Credit (Masuk)',
                        'debit' => 'Debit (Keluar)',
                    ]),
                \Filament\Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from'),
                        \Filament\Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn ($query, $date) => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['until'],
                                fn ($query, $date) => $query->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ViewMemberBalanceLedger::route('/'),
        ];
    }
}
