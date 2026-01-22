<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternalSaleResource\Pages;
use App\Models\InternalSale;
use BackedEnum;
use UnitEnum;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\Summarizers\Sum;

class InternalSaleResource extends Resource
{
    protected static ?string $model = InternalSale::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string|UnitEnum|null $navigationGroup = 'Internal Management';
    protected static ?string $navigationLabel = 'Catatan Pengeluaran';

    protected static ?string $modelLabel = 'Catatan Pengeluaran';

    public static function canAccess(): bool
    {
        if (!Auth::check()) return false;
        
        return Auth::user()->hasAnyRole(['admin', 'finance']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make()
                    ->schema([
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => Auth::id())
                            ->required(),

                        \Filament\Schemas\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('code')
                                    ->label('Kode Transaksi')
                                    ->default(fn () => 'PT-' . strtoupper(Str::random(6)))
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->readOnly(fn (string $operation) => $operation === 'create'),

                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Barang')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\DateTimePicker::make('transaction_date')
                                    ->label('Tanggal')
                                    ->required()
                                    ->default(now()),
                            ]),

                        \Filament\Schemas\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Harga Satuan')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $price = (float) $get('price');
                                        $qty = (float) $get('qty');
                                        $set('total', $price * $qty);
                                    }),

                                Forms\Components\TextInput::make('qty')
                                    ->label('Jumlah (Qty)')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $price = (float) $get('price');
                                        $qty = (float) $get('qty');
                                        $set('total', $price * $qty);
                                    }),

                                Forms\Components\TextInput::make('total')
                                    ->label('Total Harga')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->dehydrated(),
                            ]),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->label('Trx Code')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->summarize(Sum::make()->label('Grand Total')->money('IDR', locale: 'id')),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Input By')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->schema([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name', fn (Builder $query) => 
                        $query->whereHas('roles', fn ($q) => 
                            $q->whereIn('name', ['admin', 'finance', 'warehouse'])
                        )
                    )
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInternalSales::route('/'),
            'create' => Pages\CreateInternalSale::route('/create'),
            'edit' => Pages\EditInternalSale::route('/{record}/edit'),
        ];
    }
}
