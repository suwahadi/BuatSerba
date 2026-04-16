<?php

namespace App\Filament\Resources\PaymentConfirmations;

use App\Filament\Resources\PaymentConfirmations\Pages\ManagePaymentConfirmations;
use App\Models\Payment;
use App\Models\PaymentConfirmation;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class PaymentConfirmationResource extends Resource
{
    protected static ?string $model = PaymentConfirmation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?string $navigationLabel = 'Konfirmasi Pembayaran';

    protected static ?string $modelLabel = 'Konfirmasi Pembayaran';

    protected static ?string $pluralModelLabel = 'Konfirmasi Pembayaran';

    protected static ?string $slug = 'payment-confirmation';

    protected static UnitEnum|string|null $navigationGroup = null;

    protected static ?int $navigationSort = 6;

    public static function canAccess(): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('resource.payment_confirmations.view_any') || Auth::user()->hasRole('admin'));
    }

    public static function canCreate(): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('resource.payment_confirmations.create') || Auth::user()->hasRole('admin'));
    }

    public static function canEdit($record): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('resource.payment_confirmations.update') || Auth::user()->hasRole('admin'));
    }

    public static function canDelete($record): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('resource.payment_confirmations.delete') || Auth::user()->hasRole('admin'));
    }

    public static function canDeleteAny(): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('resource.payment_confirmations.delete') || Auth::user()->hasRole('admin'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Konfirmasi')
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->label('Order')
                            ->relationship('order', 'order_number')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn ($record) => $record !== null),

                        Forms\Components\TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(150),

                        Forms\Components\TextInput::make('bank')
                            ->label('Bank')
                            ->required()
                            ->maxLength(50),

                        Forms\Components\TextInput::make('nomor_rekening')
                            ->label('Nomor Rekening')
                            ->required()
                            ->maxLength(50),

                        Forms\Components\FileUpload::make('bukti_transfer_path')
                            ->label('Bukti Transfer')
                            ->disk('public')
                            ->directory('payment-proofs')
                            ->preserveFilenames()
                            ->downloadable()
                            ->openable()
                            ->required(),

                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Forms\Components\DateTimePicker::make('confirmed_at')
                            ->label('Tanggal Konfirmasi')
                            ->required(),

                        Forms\Components\Toggle::make('is_read')
                            ->label('Sudah Dibaca')
                            ->inline(false),

                        Forms\Components\DateTimePicker::make('read_at')
                            ->label('Dibaca Pada')
                            ->disabled(),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.order_number')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable()
                    ->weight(fn ($record) => $record->is_read ? FontWeight::Normal : FontWeight::Bold)
                    ->url(fn ($record) => \App\Filament\Resources\Orders\OrderResource::getUrl('edit', ['record' => $record->order_id]))
                    ->openUrlInNewTab(),

                TextColumn::make('bank')
                    ->label('Rekening')
                    ->formatStateUsing(fn ($state, $record) => "{$record->bank} - {$record->nomor_rekening}")
                    ->description(fn ($record) => "a/n {$record->nama_lengkap}")
                    ->searchable(['bank', 'nomor_rekening', 'nama_lengkap'])
                    ->sortable()
                    ->weight(fn ($record) => $record->is_read ? FontWeight::Normal : FontWeight::Bold),

                TextColumn::make('bukti_transfer_path')
                    ->label('Bukti Transfer')
                    ->formatStateUsing(fn ($state) => 'Lihat')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record) => Storage::disk('public')->url($record->bukti_transfer_path))
                    ->openUrlInNewTab(),

                TextColumn::make('catatan')
                    ->label('Catatan')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                // ToggleColumn::make('is_read')
                //     ->label('Sudah Dibaca')
                //     ->sortable()
                //     ->disabled(fn ($record) => ! static::canEdit($record)),

                TextColumn::make('confirmed_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                IconColumn::make('is_validated')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->tooltip(fn ($record) => $record->is_validated
                        ? 'Tervalidasi oleh '.($record->validatedBy?->name ?? '-').' pada '.$record->validated_at?->format('d M Y H:i')
                        : 'Belum divalidasi'
                    )
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_validated')
                    ->label('Status')
                    ->options([
                        '0' => 'Belum Divalidasi',
                        '1' => 'Sudah Divalidasi',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! isset($data['value']) || $data['value'] === null) {
                            return $query;
                        }

                        return $query->where('is_validated', (bool) ((int) $data['value']));
                    }),

                SelectFilter::make('bank')
                    ->label('Bank')
                    ->options(fn () => PaymentConfirmation::query()
                        ->select('bank')
                        ->distinct()
                        ->orderBy('bank')
                        ->pluck('bank', 'bank')
                        ->toArray())
                    ->searchable(),

                Filter::make('confirmed_at')
                    ->label('Tanggal')
                    ->schema([
                        Forms\Components\DatePicker::make('from')->label('Dari'),
                        Forms\Components\DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date) => $query->whereDate('confirmed_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date) => $query->whereDate('confirmed_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('validate_payment')
                    ->label('Validasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Validasi Pembayaran')
                    ->modalDescription(fn ($record) => 'Yakin ingin memvalidasi pembayaran untuk Order #'.($record->order?->order_number ?? '-').'? Tindakan ini akan mengubah status pembayaran menjadi lunas dan tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Validasi')
                    ->modalCancelActionLabel('Batal')
                    ->action(function ($record) {
                        if ($record->is_validated) {
                            \Filament\Notifications\Notification::make()
                                ->title('Sudah Divalidasi')
                                ->body('Konfirmasi ini sudah pernah divalidasi sebelumnya.')
                                ->warning()
                                ->send();

                            return;
                        }

                        try {
                            DB::transaction(function () use ($record) {
                                $confirmation = PaymentConfirmation::lockForUpdate()->findOrFail($record->id);

                                if ($confirmation->is_validated) {
                                    return;
                                }

                                $order = $confirmation->order;

                                if (! $order) {
                                    throw new \Exception('Order tidak ditemukan.');
                                }

                                if (in_array($order->payment_status, ['paid', 'refunded'])) {
                                    $confirmation->update([
                                        'is_validated' => true,
                                        'validated_at' => $confirmation->validated_at ?? now(),
                                        'validated_by' => Auth::id(),
                                    ]);

                                    return;
                                }

                                $payment = Payment::where('order_id', $order->id)->first();

                                if ($payment) {
                                    if (in_array($payment->transaction_status, ['settlement', 'capture'])) {
                                        $confirmation->update([
                                            'is_validated' => true,
                                            'validated_at' => $confirmation->validated_at ?? now(),
                                            'validated_by' => Auth::id(),
                                        ]);

                                        return;
                                    }

                                    $payment->update([
                                        'transaction_status' => 'settlement',
                                        'paid_at' => now(),
                                    ]);
                                } else {
                                    Payment::create([
                                        'order_id' => $order->id,
                                        'payment_gateway' => 'manual_transfer',
                                        'transaction_id' => 'manual_'.$order->order_number,
                                        'transaction_time' => now(),
                                        'transaction_status' => 'settlement',
                                        'payment_type' => 'bank_transfer',
                                        'payment_channel' => $confirmation->bank,
                                        'gross_amount' => $order->total,
                                        'currency' => 'IDR',
                                        'status_code' => '200',
                                        'status_message' => 'Manual validation by admin',
                                        'paid_at' => now(),
                                    ]);
                                }

                                $order->update([
                                    'payment_status' => 'paid',
                                    'status' => 'processing',
                                    'paid_at' => now(),
                                ]);

                                $confirmation->update([
                                    'is_validated' => true,
                                    'validated_at' => now(),
                                    'validated_by' => Auth::id(),
                                    'is_read' => true,
                                    'read_at' => $confirmation->read_at ?? now(),
                                ]);
                            });

                            \Filament\Notifications\Notification::make()
                                ->title('Pembayaran Tervalidasi')
                                ->body('Pembayaran Order #'.$record->order?->order_number.' berhasil divalidasi.')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Log::error('Failed to validate payment confirmation', [
                                'confirmation_id' => $record->id,
                                'order_id' => $record->order_id,
                                'error' => $e->getMessage(),
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title('Validasi Gagal')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->disabled(fn ($record) => $record->is_validated)
                    ->tooltip(fn ($record) => $record->is_validated ? 'Sudah divalidasi' : 'Klik untuk validasi pembayaran')
                    ->visible(fn ($record) => Auth::check() && (Auth::user()->hasPermissionTo('resource.payment_confirmations.update') || Auth::user()->hasRole('admin'))),

                \Filament\Actions\Action::make('view')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detail Konfirmasi Pembayaran')
                    ->modalWidth('lg')
                    ->action(function () {
                        //
                    })
                    ->mountUsing(function ($record) {
                        if ($record && ! $record->is_read) {
                            $record->update([
                                'is_read' => true,
                                'read_at' => now(),
                            ]);
                        }
                    })
                    ->modalContent(function ($record) {
                        return new \Illuminate\Support\HtmlString(view('filament.resources.payment-confirmations.view', ['record' => $record])->render());
                    })
                    ->modalActions([])
                    ->visible(fn ($record) => Auth::check() && (Auth::user()->hasPermissionTo('resource.payment_confirmations.view') || Auth::user()->hasRole('admin'))),

                \Filament\Actions\EditAction::make()
                    ->label('Ubah')
                    ->visible(fn ($record) => static::canEdit($record)),

                \Filament\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->visible(fn ($record) => static::canDelete($record)),
            ])
            // ->bulkActions([
            //     \Filament\Actions\BulkActionGroup::make([
            //         \Filament\Actions\DeleteBulkAction::make()
            //             ->visible(fn () => static::canDeleteAny()),
            //     ]),
            // ])
            ->defaultSort('confirmed_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePaymentConfirmations::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) PaymentConfirmation::query()->where('is_read', false)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return PaymentConfirmation::query()->where('is_read', false)->count() > 0 ? 'warning' : null;
    }
}
