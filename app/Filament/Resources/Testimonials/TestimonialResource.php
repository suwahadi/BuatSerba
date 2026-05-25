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
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
                Section::make('Gambar Testimoni')
                    ->description('Gambar tampil pada slider testimoni di halaman home. Disimpan sebagai WebP terkompresi tanpa resize.')
                    ->schema([
                        FileUpload::make('image')
                            ->required()
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('testimonials')
                            ->maxSize(8192)
                            ->imagePreviewHeight('200')
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $component): string {
                                $filename = Str::random(20).'.webp';
                                $image = Image::read($file->getRealPath())->toWebp(88);
                                $path = $component->getDirectory().'/'.$filename;
                                Storage::disk($component->getDiskName())->put($path, (string) $image);

                                return $path;
                            })
                            ->label('Gambar')
                            ->helperText('Maks 8MB. Resolusi asli dipertahankan, hanya dikonversi ke WebP agar load lebih cepat.')
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->default(true)
                            ->label('Aktif')
                            ->inline(false)
                            ->columnSpan(1),

                        TextInput::make('sort')
                            ->numeric()
                            ->default(0)
                            ->label('Urutan')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Section::make('Info Tambahan (opsional)')
                    ->description('Data ini tidak ditampilkan di slider testimoni saat ini. Boleh dikosongkan.')
                    ->collapsed()
                    ->schema([
                        TextInput::make('name')
                            ->maxLength(255)
                            ->label('Nama')
                            ->columnSpan(1),

                        TextInput::make('location')
                            ->maxLength(255)
                            ->label('Lokasi')
                            ->columnSpan(1),

                        Textarea::make('content')
                            ->rows(3)
                            ->label('Isi Testimoni')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('image')
                    ->square()
                    ->label('Gambar')
                    ->disk('public'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('location')
                    ->searchable()
                    ->label('Lokasi')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

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
            ->defaultSort('sort', 'asc')
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
