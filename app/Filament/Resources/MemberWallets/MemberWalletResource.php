<?php

namespace App\Filament\Resources\MemberWallets;

use App\Filament\Resources\MemberWallets\Pages\ManageMemberWallets;
use App\Models\MemberWallet;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class MemberWalletResource extends Resource
{
    protected static ?string $model = MemberWallet::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wallet';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'Dompet Member';

    protected static UnitEnum|string|null $navigationGroup = null;

    protected static ?string $modelLabel = 'Dompet Member';

    protected static ?string $pluralModelLabel = 'Dompet Member';

    protected static ?string $recordTitleAttribute = 'user.name';

    public static function canAccess(): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('resource.member_wallets.view_any') || Auth::user()->hasRole('admin'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->getOptionLabelUsing(function ($value) {
                        $user = User::find($value);
                        return $user ? $user->name . ' (' . $user->email . ')' : $value;
                    }),
                TextInput::make('balance')
                    ->label('Saldo')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->step(0.01),
                TextInput::make('locked_balance')
                    ->label('Saldo Terkunci')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->step(0.01),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Member')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('initial_balance')
                    ->label('Saldo Awal')
                    ->money('IDR')
                    ->sortable()
                    ->color('success'),
                TextColumn::make('locked_balance')
                    ->label('Saldo Terkunci')
                    ->money('IDR')
                    ->sortable()
                    ->color('warning'),
                TextColumn::make('available_balance')
                    ->label('Saldo Tersedia')
                    ->money('IDR')
                    ->sortable()
                    ->color('primary'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMemberWallets::route('/'),
        ];
    }
}
