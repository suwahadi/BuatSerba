<?php

namespace App\Filament\Resources\ReturnRequests;

use App\Enums\ReturnStatus;
use App\Filament\Resources\ReturnRequests\Pages\ListReturnRequests;
use App\Filament\Resources\ReturnRequests\Pages\ViewReturnRequest;
use App\Models\ReturnRequest;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ReturnRequestResource extends Resource
{
    protected static ?string $model = ReturnRequest::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Retur Barang';

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Pesanan';

    protected static ?string $recordTitleAttribute = 'order_number';

    protected static ?string $modelLabel = 'Permohonan Retur';

    protected static ?string $pluralModelLabel = 'Permohonan Retur';

    public static function canAccess(): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('resource.return_requests.view_any') || Auth::user()->hasRole('admin'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Permohonan Retur')
                    ->schema([
                        TextInput::make('order_number')
                            ->label('Nomor Pesanan')
                            ->readOnly(),
                        TextInput::make('user.name')
                            ->label('Nama User')
                            ->readOnly(),
                        TextInput::make('user.email')
                            ->label('Email User')
                            ->readOnly(),
                        BadgeColumn::make('status')
                            ->label('Status')
                            ->formatStateUsing(fn (ReturnStatus $state) => $state->label())
                            ->color(fn (ReturnStatus $state) => match ($state) {
                                ReturnStatus::APPROVED => 'success',
                                ReturnStatus::PENDING => 'warning',
                                ReturnStatus::REJECTED => 'danger',
                            }),
                    ])->columns(2),

                Section::make('Barang yang Diretur')
                    ->schema([
                        TextInput::make('items_display')
                            ->label('Daftar Barang')
                            ->readOnly()
                            ->columnSpanFull()
                            ->formatStateUsing(function ($record) {
                                if (! $record || $record->items->count() === 0) {
                                    return '-';
                                }

                                return $record->items
                                    ->map(fn ($item) => "{$item->orderItem->product_name} ({$item->orderItem->sku_code}) - Qty: {$item->quantity}")
                                    ->join(', ');
                            }),
                    ]),

                Section::make('Catatan')
                    ->schema([
                        Textarea::make('note')
                            ->label('Catatan User')
                            ->readOnly()
                            ->columnSpanFull(),
                    ])
                    ->collapsed()
                    ->collapsible(),

                Section::make('Proses Admin')
                    ->schema([
                        Textarea::make('admin_note')
                            ->label('Catatan Admin')
                            ->columnSpanFull(),
                        TextInput::make('handled_by_name')
                            ->label('Diproses Oleh')
                            ->readOnly(),
                        TextInput::make('handled_at')
                            ->label('Tanggal Diproses')
                            ->readOnly(),
                    ])
                    ->visible(fn ($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Nomor Pesanan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Nama User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email User')
                    ->searchable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (ReturnStatus $state) => $state->label())
                    ->color(fn (ReturnStatus $state) => match ($state) {
                        ReturnStatus::APPROVED => 'success',
                        ReturnStatus::PENDING => 'warning',
                        ReturnStatus::REJECTED => 'danger',
                    }),
                TextColumn::make('created_at')
                    ->label('Tanggal Ajuan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('handled_at')
                    ->label('Tanggal Diproses')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(ReturnStatus::cases())
                        ->mapWithKeys(fn (ReturnStatus $case) => [$case->value => $case->label()])
                        ->toArray()),
                Filter::make('pending_only')
                    ->label('Hanya Menunggu Persetujuan')
                    ->query(fn (Builder $query) => $query->where('status', 'pending')),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Setujui')
                    ->color('success')
                    ->icon('heroicon-m-check-circle')
                    ->visible(fn (ReturnRequest $record) => $record->status === ReturnStatus::PENDING)
                    ->action(function (ReturnRequest $record) {
                        $record->update([
                            'status' => ReturnStatus::APPROVED,
                            'handled_by' => Auth::id(),
                            'handled_at' => now(),
                        ]);
                    })
                    ->successNotificationTitle('Permohonan retur disetujui'),
                Action::make('reject')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-m-x-circle')
                    ->visible(fn (ReturnRequest $record) => $record->status === ReturnStatus::PENDING)
                    ->form([
                        Textarea::make('admin_note')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->action(function (ReturnRequest $record, array $data) {
                        $record->update([
                            'status' => ReturnStatus::REJECTED,
                            'admin_note' => $data['admin_note'],
                            'handled_by' => Auth::id(),
                            'handled_at' => now(),
                        ]);
                    })
                    ->successNotificationTitle('Permohonan retur ditolak'),
                \Filament\Actions\ViewAction::make()
                    ->label('Lihat Detail'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReturnRequests::route('/'),
            'view' => ViewReturnRequest::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
