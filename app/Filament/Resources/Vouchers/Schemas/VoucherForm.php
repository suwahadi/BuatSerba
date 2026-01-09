<?php

namespace App\Filament\Resources\Vouchers\Schemas;

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
                            ->maxLength(191)
                            ->label('Nama Voucher')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('voucher_code')
                            ->required()
                            ->maxLength(64)
                            ->unique(ignoreRecord: true)
                            ->label('Kode Voucher')
                            ->helperText('Kode unik yang akan diinput pengguna')
                            ->columnSpan(1),

                        \Filament\Forms\Components\FileUpload::make('image')
                            ->image()
                            ->maxSize(2048)
                            ->disk('public')
                            ->visibility('public')
                            ->directory('vouchers')
                            ->label('Gambar Voucher (Opsional)')
                            ->columnSpanFull(),
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

                \Filament\Schemas\Components\Section::make('Konfigurasi Diskon')
                    ->description('Konfigurasi tipe dan jumlah diskon')
                    ->schema([
                        \Filament\Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'number' => 'Nominal (Rp)',
                                'percentage' => 'Persentase (%)',
                            ])
                            ->default('number')
                            ->label('Tipe Diskon')
                            ->reactive()
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->label('Jumlah Diskon')
                            ->helperText(fn ($get) => $get('type') === 'percentage' ? 'Masukkan nilai 1-100 untuk persentase' : 'Masukkan nominal dalam Rupiah')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Pembatasan')
                    ->description('Terapkan pembatasan voucher')
                    ->schema([
                        \Filament\Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->label('User Spesifik')
                            ->helperText('Kosongkan jika voucher berlaku untuk semua user')
                            ->columnSpan(1),

                        \Filament\Forms\Components\Toggle::make('is_free_shipment')
                            ->default(false)
                            ->label('Gratis Ongkir')
                            ->helperText('Aktifkan jika voucher memberikan gratis ongkir')
                            ->inline(false)
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                \Filament\Schemas\Components\Section::make('Status & Tampilan')
                    ->description('Kelola status dan urutan voucher')
                    ->schema([
                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Aktif')
                            ->inline(false),

                        \Filament\Forms\Components\TextInput::make('sort')
                            ->numeric()
                            ->default(0)
                            ->label('Urutan'),
                    ])
                    ->columns(2),
            ]);
    }
}
