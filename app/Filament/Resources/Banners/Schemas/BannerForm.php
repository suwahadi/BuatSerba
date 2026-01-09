<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Banner Information')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(191)
                            ->label('Title')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('url')
                            ->maxLength(2048)
                            ->label('URL')
                            ->columnSpan(1),

                        \Filament\Forms\Components\FileUpload::make('image')
                            ->required()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg'])
                            ->maxSize(10240)
                            ->disk('public')
                            ->visibility('public')
                            ->directory('banners')
                            ->label('Banner Image')
                            ->helperText('Ukuran file maksimal 10MB')
                            ->imageResizeUpscale(false)
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $component): string {
                                $filename = \Illuminate\Support\Str::random(40).'.webp';

                                $image = Image::read($file->getRealPath())
                                    ->cover(1600, 600)
                                    ->toWebp(90);

                                $path = $component->getDirectory().'/'.$filename;

                                Storage::disk($component->getDiskName())->put(
                                    $path,
                                    (string) $image
                                );

                                return $path;
                            })
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->label('Description')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Active')
                            ->inline(false)
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('sort')
                            ->numeric()
                            ->default(0)
                            ->label('Sort Order')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
