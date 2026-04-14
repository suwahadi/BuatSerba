<?php

namespace App\Filament\Resources\BlogPosts;

use App\Filament\Resources\BlogPosts\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPosts\Pages\EditBlogPost;
use App\Filament\Resources\BlogPosts\Pages\ListBlogPosts;
use App\Models\BlogPost;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
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

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Pos';

    protected static UnitEnum|string|null $navigationGroup = 'Blog';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Pos Blog';

    protected static ?string $pluralModelLabel = 'Pos Blog';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Informasi Artikel')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Judul')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, callable $set, $get) {
                                        if ($operation === 'create' && empty($get('slug'))) {
                                            $set('slug', BlogPost::generateUniqueSlug($state));
                                        }
                                    }),

                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->label('Slug'),

                                Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Kategori'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),

                Group::make()
                    ->schema([
                        Section::make('Status & Publikasi')
                            ->schema([
                                Toggle::make('is_active')
                                    ->default(true)
                                    ->label('Aktif')
                                    ->inline(false),

                                DateTimePicker::make('published_at')
                                    ->label('Tanggal Publikasi')
                                    ->default(now()),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),

                Section::make('Thumbnail')
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->label('Thumbnail')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg'])
                            ->maxSize(10240)
                            ->disk('public')
                            ->visibility('public')
                            ->directory('blog')
                            ->helperText('Ukuran optimal: 1200x800px')
                            ->imageResizeUpscale(false)
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $component, $get): string {
                                $slug = $get('slug') ?? \Illuminate\Support\Str::random(10);
                                $randomPrefix = \Illuminate\Support\Str::random(6);
                                $filename = $randomPrefix . '-' . $slug . '.webp';
                                $image = Image::read($file->getRealPath())
                                    ->cover(1200, 800)
                                    ->toWebp(90);
                                $path = $component->getDirectory() . '/' . $filename;
                                Storage::disk($component->getDiskName())->put($path, (string) $image);
                                return $path;
                            })
                            ->dehydrated(fn ($state) => $state !== null),
                    ])
                    ->columnSpanFull(),

                Section::make('Konten')
                    ->schema([
                        RichEditor::make('content')
                            ->required()
                            ->label('Konten')
                            ->minLength(100)
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'codeBlock',
                                'h2',
                                'h3',
                                'undo',
                                'redo',
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('SEO Meta Tags')
                    ->description('Konfigurasi meta tags untuk optimasi SEO')
                    ->schema([
                        Repeater::make('meta_seo')
                            ->label('Meta SEO')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Meta Title (Judul Halaman)')
                                    ->maxLength(60)
                                    ->placeholder('Contoh: Best Healthy Recipes for 2026 | MyBlog'),

                                TextInput::make('description')
                                    ->label('Meta Description')
                                    ->maxLength(160)
                                    ->placeholder('Contoh: Discover easy, healthy recipes for 2026...'),

                                TextInput::make('og_title')
                                    ->label('Open Graph Title')
                                    ->maxLength(60)
                                    ->placeholder('Biarkan kosong untuk menggunakan title default'),

                                TextInput::make('og_description')
                                    ->label('Open Graph Description')
                                    ->maxLength(160)
                                    ->placeholder('Biarkan kosong untuk menggunakan description default'),

                                TextInput::make('og_image')
                                    ->label('Open Graph Image URL')
                                    ->url()
                                    ->placeholder('https://example.com/image.jpg'),
                            ])
                            ->collapsible()
                            ->collapsed(false)
                            ->cloneable(false)
                            ->reorderable(false)
                            ->maxItems(1)
                            ->addActionLabel('Tambah Konfigurasi SEO')
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'SEO Configuration'),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->disk('public'),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->label('Judul'),
                TextColumn::make('category.name')
                    ->searchable()
                    ->sortable()
                    ->label('Kategori'),
                TextColumn::make('published_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->label('Publikasi'),
                TextColumn::make('view_count')
                    ->numeric()
                    ->sortable()
                    ->label('Dilihat'),
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogPosts::route('/'),
            'create' => CreateBlogPost::route('/create'),
            'edit' => EditBlogPost::route('/{record}/edit'),
        ];
    }
}
