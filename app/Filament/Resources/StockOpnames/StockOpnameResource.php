<?php

namespace App\Filament\Resources\StockOpnames;

use App\Filament\Resources\StockOpnames\Pages\CreateStockOpname;
use App\Filament\Resources\StockOpnames\Pages\ListStockOpnames;
use App\Filament\Resources\StockOpnames\Pages\ViewStockOpname;
use App\Models\StockOpname;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use UnitEnum;

class StockOpnameResource extends Resource
{
    protected static ?string $model = StockOpname::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem Internal';

    protected static ?string $navigationLabel = 'Stok Opname';

    protected static ?string $modelLabel = 'Stok Opname';

    protected static ?string $pluralModelLabel = 'Stok Opname';

    protected static ?string $slug = 'stock-opname';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return Auth::user()->hasPermissionTo('resource.stock_opnames.view_any') || Auth::user()->hasRole('admin');
    }

    public static function getNavigationBadge(): ?string
    {
        $unadjustedCount = StockOpname::where('is_adjusted', false)->count();

        return $unadjustedCount > 0 ? (string) $unadjustedCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $unadjustedCount = StockOpname::where('is_adjusted', false)->count();

        return $unadjustedCount > 0 ? 'warning' : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('opname_date')
                    ->label('Tanggal')
                    ->date('d-m-Y ')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Operator')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        $userName = $record->user?->name ?? '-';
                        $roleName = $record->user?->roles?->first()?->name ?? '-';

                        return new HtmlString(
                            view('filament.components.operator-badge', compact('userName', 'roleName'))->render()
                        );
                    })
                    ->html(),
                TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->sortable(),
                TextColumn::make('notes')
                    ->label('Keterangan')
                    ->limit(50)
                    ->placeholder('-'),
                \Filament\Tables\Columns\IconColumn::make('is_adjusted')
                    ->label('Status')
                    ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-arrow-path')
                    ->color(fn ($state) => $state ? 'success' : 'warning'),
                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d-m-Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('branch_id')
                    ->label('Cabang')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('opname_date')
                    ->schema([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date) => $query->whereDate('opname_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date) => $query->whereDate('opname_date', '<=', $date),
                            );
                    }),
                Filter::make('is_adjusted')
                    ->label('Status Sinkronisasi')
                    ->schema([
                        Select::make('is_adjusted')
                            ->label('Status Sinkronisasi')
                            ->options([
                                'yes' => 'Sudah Disesuaikan',
                                'no' => 'Belum Disesuaikan',
                            ])
                            ->default(''),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['is_adjusted'],
                                fn (Builder $query, $status) => $query->where('is_adjusted', $status === 'yes'),
                            );
                    }),
                Filter::make('role')
                    ->label('Staff')
                    ->schema([
                        Select::make('role')
                            ->label('Operator')
                            ->options(\Spatie\Permission\Models\Role::all()->mapWithKeys(fn ($role) => [$role->name => ucfirst($role->name)]))
                            ->placeholder('Semua'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['role'],
                                fn (Builder $query, $role) => $query->whereHas('user.roles', fn ($q) => $q->where('name', $role)),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Detail'),
                DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockOpnames::route('/'),
            'create' => CreateStockOpname::route('/create'),
            'view' => ViewStockOpname::route('/{record}'),
        ];
    }
}
