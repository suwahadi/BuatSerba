<?php

namespace App\Filament\Resources\Vouchers\Schemas;

use Filament\Actions\Action;
use Filament\Schemas\Schema;

class VoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Voucher')
                    ->description('Detail dasar voucher')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('voucher_name')
                            ->required()
                            ->maxLength(100)
                            ->label('Nama Voucher')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('voucher_code')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->label('Kode Voucher (Unik)')
                            ->default(fn () => strtoupper(\Illuminate\Support\Str::random(8)))
                            ->suffixAction(
                                Action::make('regenerate')
                                    ->icon('heroicon-o-arrow-path')
                                    ->action(function ($set) {
                                        $set('voucher_code', strtoupper(\Illuminate\Support\Str::random(8)));
                                    })
                            )
                            ->columnSpan(1),

                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Aktif')
                            ->inline(false),

                        \Filament\Forms\Components\TextInput::make('sort')
                            ->numeric()
                            ->default(0)
                            ->label('Urutan Tampil'),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Periode Berlaku')
                    ->description('Atur tanggal berlaku voucher (kosongkan untuk tidak terbatas)')
                    ->schema([
                        \Filament\Forms\Components\DateTimePicker::make('valid_start')
                            ->label('Berlaku Dari')
                            ->helperText('Kosongkan jika langsung berlaku'),

                        \Filament\Forms\Components\DateTimePicker::make('valid_end')
                            ->label('Berlaku Sampai')
                            ->helperText('Kosongkan jika tidak ada batas akhir'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                \Filament\Schemas\Components\Section::make('Banner')
                    ->schema([
                        \Filament\Forms\Components\FileUpload::make('image')
                            ->image()
                            ->maxSize(2048)
                            ->disk('public')
                            ->visibility('public')
                            ->directory('vouchers')
                            ->label('Gambar Voucher (Opsional)')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                \Filament\Schemas\Components\Section::make('Konfigurasi Diskon')
                    ->description('Konfigurasi tipe dan jumlah diskon')
                    ->schema([
                        \Filament\Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'fixed' => 'Nominal (Rp)',
                                'percentage' => 'Persentase (%)',
                            ])
                            ->default('fixed')
                            ->label('Tipe Diskon')
                            ->live()
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->label('Jumlah Diskon')
                            ->helperText(fn ($get) => $get('type') === 'percentage' ? 'Masukkan nilai 1-100' : 'Masukkan nominal dalam Rupiah')
                            ->columnSpan(1),
                            
                        \Filament\Forms\Components\TextInput::make('max_discount_amount')
                            ->numeric()
                            ->label('Maksimal Diskon')
                            ->prefix('Rp')
                            ->helperText('Maksimal nominal potongan jika menggunakan persentase')
                            ->visible(fn ($get) => $get('type') === 'percentage')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('min_spend')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->label('Minimal Belanja')
                            ->prefix('Rp')
                            ->helperText('Total belanja minimal agar voucher bisa digunakan')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Pembatasan & Target User')
                    ->description('Atur siapa yang bisa menggunakan voucher ini')
                    ->schema([
                        \Filament\Forms\Components\Toggle::make('is_new_user_only')
                            ->default(false)
                            ->label('Khusus Pengguna Baru')
                            ->helperText('Hanya berlaku untuk transaksi pertama user')
                            ->inline(false)
                            ->columnSpan(1),

                        \Filament\Forms\Components\Toggle::make('is_free_shipment')
                            ->default(false)
                            ->label('Potongan Ongkir')
                            ->helperText('Aktifkan jika voucher ini berupa potongan ongkir')
                            ->inline(false)
                            ->columnSpan(1),

                        \Filament\Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->label('User Spesifik')
                            ->helperText('Kosongkan jika voucher berlaku untuk semua user')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('usage_limit')
                            ->numeric()
                            ->label('Kuota Global')
                            ->helperText('Total maksimal penggunaan voucher ini (kosongkan jika unlimited)')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('limit_per_user')
                            ->numeric()
                            ->default(1)
                            ->label('Limit Per User')
                            ->helperText('Maksimal penggunaan per satu user')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('usage_count')
                            ->numeric()
                            ->label('Telah Digunakan')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

            ]);
    }
}
