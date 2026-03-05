<?php

namespace App\Filament\Resources\ReturnRequests;

use App\Enums\ReturnStatus;
use App\Filament\Resources\ReturnRequests\Pages\ListReturnRequests;
use App\Filament\Resources\ReturnRequests\Pages\ViewReturnRequest;
use App\Models\ReturnRequest;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
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
                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make('Informasi Permohonan')
                            ->schema([
                                Placeholder::make('order_number_display')
                                    ->label('Nomor Pesanan')
                                    ->content(fn ($record) => $record?->order_number ?? '-'),
                                Placeholder::make('user_name_display')
                                    ->label('Nama Pelanggan')
                                    ->content(fn ($record) => $record?->user?->name ?? '-'),
                                Placeholder::make('user_email_display')
                                    ->label('Email')
                                    ->content(fn ($record) => $record?->user?->email ?? '-'),
                                Placeholder::make('created_at_display')
                                    ->label('Tanggal Ajuan')
                                    ->content(fn ($record) => $record?->created_at?->format('d M Y H:i') ?? '-'),
                                Placeholder::make('status_badge')
                                    ->label('Status')
                                    ->content(function ($record) {
                                        if (! $record) {
                                            return '-';
                                        }
                                        $status = $record->status;
                                        $color = match ($status) {
                                            ReturnStatus::APPROVED => 'bg-green-100 text-green-800',
                                            ReturnStatus::PENDING => 'bg-yellow-100 text-yellow-800',
                                            ReturnStatus::REJECTED => 'bg-red-100 text-red-800',
                                        };

                                        return new HtmlString("<span class=\"inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {$color}\">{$status->label()}</span>");
                                    }),
                            ])
                            ->columns(2),

                        Section::make('Barang yang Diretur')
                            ->schema([
                                Placeholder::make('items_display')
                                    ->hiddenLabel()
                                    ->content(function ($record) {
                                        if (! $record || $record->items->count() === 0) {
                                            return '-';
                                        }

                                        $items = $record->items->map(function ($item) {
                                            return "<div class=\"py-1\">• {$item->orderItem->product_name} <span class=\"text-gray-500\">({$item->orderItem->sku_code})</span> - Qty: {$item->quantity}</div>";
                                        })->join('');

                                        return new HtmlString($items);
                                    }),
                            ]),

                        Section::make('Catatan Pelanggan')
                            ->schema([
                                Placeholder::make('note_display')
                                    ->hiddenLabel()
                                    ->content(fn ($record) => $record?->note ?: new HtmlString('<span class="text-gray-400 italic">Tidak ada catatan</span>')),
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 2]),

                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make('Proses Admin')
                            ->schema([
                                Textarea::make('admin_note')
                                    ->label('Catatan Admin')
                                    ->rows(3),
                                Placeholder::make('handled_by_name')
                                    ->label('Diproses Oleh')
                                    ->content(fn ($record) => $record?->handledBy?->name ?? '-'),
                                Placeholder::make('handled_at_display')
                                    ->label('Tanggal Diproses')
                                    ->content(fn ($record) => $record?->handled_at?->format('d M Y H:i') ?? '-'),
                            ]),

                        Section::make('Bukti Foto')
                            ->schema([
                                Placeholder::make('image_proof_display')
                                    ->hiddenLabel()
                                    ->content(function ($record) {
                                        if (! $record || empty($record->image_proof)) {
                                            return new HtmlString('<span class="text-gray-400 italic">Tidak ada bukti foto</span>');
                                        }

                                        $images = collect($record->image_proof)->map(function ($path) {
                                            $url = Storage::url($path);

                                            return "<a href=\"{$url}\" target=\"_blank\" class=\"inline-block\"><img src=\"{$url}\" alt=\"Bukti\" class=\"w-24 h-24 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition\"></a>";
                                        })->join(' ');

                                        return new HtmlString("<div class=\"flex gap-3 flex-wrap\">{$images}</div>");
                                    }),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
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
                ImageColumn::make('image_proof')
                    ->label('Bukti')
                    ->disk('public')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->getStateUsing(fn (ReturnRequest $record) => $record->image_proof ?? []),
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
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Persetujuan')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui permohonan retur ini?')
                    ->modalSubmitActionLabel('Ya, Setujui')
                    ->modalCancelActionLabel('Tidak')
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
