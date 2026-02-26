<?php

namespace App\Filament\Resources\Testimonials;

use App\Filament\Resources\Testimonials\Pages\ManageTestimonials;
use App\Models\Testimonial;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?string $navigationLabel = 'Testimoni';

    protected static UnitEnum|string|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 7;

    protected static ?string $modelLabel = 'Testimoni';

    protected static ?string $pluralModelLabel = 'Testimoni';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama'),
                TextInput::make('location')
                    ->required()
                    ->maxLength(255)
                    ->label('Lokasi'),
                FileUpload::make('image')
                    ->avatar()
                    ->disk('public')
                    ->visibility('public')
                    ->directory('testimonials')
                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $component): string {
                        $filename = \Illuminate\Support\Str::random(20).'.webp';
                        $image = Image::read($file->getRealPath())
                            ->cover(200, 200)
                            ->toWebp(90);
                        $path = $component->getDirectory().'/'.$filename;
                        Storage::disk($component->getDiskName())->put(
                            $path,
                            (string) $image
                        );

                        return $path;
                    })
                    ->label('Foto'),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull()
                    ->label('Isi Testimoni'),
                TextInput::make('sort')
                    ->numeric()
                    ->default(0)
                    ->columnSpanFull()
                    ->label('Urutan'),
                Toggle::make('is_active')
                    ->default(true)
                    ->columnSpanFull()
                    ->label('Aktif'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('image')
                    ->circular()
                    ->label('Foto')
                    ->disk('public'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama'),
                TextColumn::make('location')
                    ->searchable()
                    ->label('Lokasi'),
                TextColumn::make('sort')
                    ->sortable()
                    ->label('Urutan'),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTestimonials::route('/'),
        ];
    }
}
