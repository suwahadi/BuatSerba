<?php

namespace App\Filament\Resources\FlashSales\Schemas;

use App\Models\Sku;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class FlashSaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Sesi')
                    ->description('Detail dasar sesi flash sale')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(120)
                            ->label('Nama Sesi')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if (! $get('slug')) {
                                    $set('slug', Str::slug($state ?? ''));
                                }
                            })
                            ->columnSpan(1),

                        TextInput::make('slug')
                            ->maxLength(140)
                            ->unique(ignoreRecord: true)
                            ->label('Slug')
                            ->helperText('Otomatis dari nama bila dikosongkan')
                            ->columnSpan(1),

                        TextInput::make('tagline')
                            ->maxLength(180)
                            ->label('Tagline')
                            ->placeholder('mis. Hari Ini Saja')
                            ->columnSpan(1),

                        TextInput::make('sort')
                            ->numeric()
                            ->default(0)
                            ->label('Urutan Tampil')
                            ->helperText('Bila ada beberapa sesi aktif, ambil yang terkecil')
                            ->columnSpan(1),

                        Toggle::make('is_active')
                            ->default(true)
                            ->label('Aktif')
                            ->inline(false),
                    ])
                    ->columns(2),

                Section::make('Periode & Countdown')
                    ->description('Sesi hanya tampil di storefront jika is_active=true DAN now BETWEEN starts_at..ends_at')
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->required()
                            ->label('Mulai')
                            ->seconds(false)
                            ->columnSpan(1),

                        DateTimePicker::make('ends_at')
                            ->required()
                            ->label('Berakhir')
                            ->seconds(false)
                            ->after('starts_at')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Section::make('Banner')
                    ->schema([
                        FileUpload::make('banner_image')
                            ->image()
                            ->maxSize(4096)
                            ->disk('public')
                            ->visibility('public')
                            ->directory('flash-sales')
                            ->label('Gambar Banner (Opsional)')
                            ->helperText('Background gradient default tetap dipakai bila dikosongkan')
                            ->imageResizeUpscale(false)
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $component): string {
                                $filename = Str::random(20).'.webp';
                                $image = Image::read($file->getRealPath());
                                $maxWidth = 1600;
                                $origWidth = method_exists($image, 'width') ? $image->width() : null;
                                $origHeight = method_exists($image, 'height') ? $image->height() : null;

                                if ($origWidth && $origWidth > $maxWidth && $origHeight) {
                                    $newWidth = $maxWidth;
                                    $newHeight = (int) round($origHeight * ($newWidth / $origWidth));
                                    if (method_exists($image, 'resize')) {
                                        $image = $image->resize($newWidth, $newHeight);
                                    }
                                }

                                $image = $image->toWebp(90);
                                $path = $component->getDirectory().'/'.$filename;
                                Storage::disk($component->getDiskName())->put($path, (string) $image);

                                return $path;
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Item Flash Sale')
                    ->description('Tambahkan SKU yang dijual selama sesi ini, beserta harga spesial dan kuota.')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->label('Daftar Item')
                            ->schema([
                                Select::make('sku_id')
                                    ->label('SKU')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->options(function () {
                                        return Sku::with('product:id,name')
                                            ->where('is_active', true)
                                            ->get()
                                            ->mapWithKeys(function (Sku $sku) {
                                                $attrName = $sku->getAttributes()['attributes'] ?? null;
                                                $variant = '';
                                                if (is_string($attrName)) {
                                                    $decoded = json_decode($attrName, true);
                                                    if (is_array($decoded) && ! empty($decoded['name'])) {
                                                        $variant = ' — '.$decoded['name'];
                                                    }
                                                }
                                                $productName = $sku->product?->name ?? 'Unknown';

                                                return [$sku->id => $productName.$variant.' ('.$sku->sku.')'];
                                            })
                                            ->toArray();
                                    })
                                    ->afterStateUpdated(function ($state, $set) {
                                        if (! $state) {
                                            return;
                                        }
                                        $sku = Sku::find($state);
                                        if ($sku) {
                                            $set('original_price_snapshot', (float) $sku->selling_price);
                                        }
                                    })
                                    ->columnSpan(2),

                                TextInput::make('flash_price')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('Rp')
                                    ->label('Harga Flash')
                                    ->rules([
                                        function ($get) {
                                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                $original = (float) $get('original_price_snapshot');
                                                if ($original > 0 && (float) $value >= $original) {
                                                    $fail('Harga flash harus lebih kecil dari harga normal.');
                                                }
                                            };
                                        },
                                    ])
                                    ->columnSpan(1),

                                TextInput::make('original_price_snapshot')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('Rp')
                                    ->label('Harga Normal (Snapshot)')
                                    ->helperText('Otomatis dari selling_price SKU, bisa disesuaikan')
                                    ->columnSpan(1),

                                TextInput::make('stock_limit')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->label('Kuota Stok')
                                    ->helperText('Jumlah unit yang dialokasikan untuk Flash Sale')
                                    ->columnSpan(1),

                                TextInput::make('sold_count')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->label('Terjual')
                                    ->columnSpan(1),

                                TextInput::make('sort')
                                    ->numeric()
                                    ->default(0)
                                    ->label('Urutan')
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => isset($state['sku_id'])
                                ? optional(Sku::with('product:id,name')->find($state['sku_id']))->product?->name
                                : null)
                            ->collapsible()
                            ->addActionLabel('Tambah Item')
                            ->defaultItems(0)
                            ->reorderable()
                            ->orderColumn('sort'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
