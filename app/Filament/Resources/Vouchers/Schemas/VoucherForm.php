<?php

namespace App\Filament\Resources\Vouchers\Schemas;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
                            ->imageResizeUpscale(false)
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $component): string {
                                $filename = \Illuminate\Support\Str::random(20).'.webp';
                                $image = Image::read($file->getRealPath());
                                $maxWidth = 800;
                                $origWidth = method_exists($image, 'width') ? $image->width() : ($image->getWidth() ?? null);
                                $origHeight = method_exists($image, 'height') ? $image->height() : ($image->getHeight() ?? null);

                                if ($origWidth && $origWidth > $maxWidth) {
                                    $newWidth = $maxWidth;
                                    $newHeight = (int) round($origHeight * ($newWidth / $origWidth));
                                    if (method_exists($image, 'resize')) {
                                        $image = $image->resize($newWidth, $newHeight);
                                    } else {
                                        $image = $image->modify(new \Intervention\Image\Vips\Modifier\ResizeModifier($newWidth, $newHeight));
                                    }
                                }

                                $image = $image->toWebp(90);
                                $path = $component->getDirectory().'/'.$filename;
                                Storage::disk($component->getDiskName())->put(
                                    $path,
                                    (string) $image
                                );

                                return $path;
                            })
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

                \Filament\Schemas\Components\Section::make('Konfigurasi Cashback')
                    ->description('Atur cashback yang akan diberikan ke saldo member')
                    ->schema([
                        \Filament\Forms\Components\Select::make('cashback_type')
                            ->label('Tipe Cashback')
                            ->options([
                                '' => 'Tidak Ada Cashback',
                                'fixed' => 'Nominal Tetap (Rp)',
                                'percentage' => 'Persentase (%)',
                            ])
                            ->live()
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('cashback_amount')
                            ->label('Nominal Cashback')
                            ->numeric()
                            ->prefix('Rp')
                            ->visible(fn ($get) => $get('cashback_type') === 'fixed')
                            ->helperText('Nominal cashback yang akan diberikan')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('cashback_percentage')
                            ->label('Persentase Cashback')
                            ->numeric()
                            ->suffix('%')
                            ->visible(fn ($get) => $get('cashback_type') === 'percentage')
                            ->helperText('Persentase dari subtotal yang akan dicashback')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

            ]);
    }
}
