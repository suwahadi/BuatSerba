<?php

namespace App\Filament\Resources\MasterProducts;

use App\Filament\Resources\MasterProducts\Pages\CreateMasterProduct;
use App\Filament\Resources\MasterProducts\Pages\EditMasterProduct;
use App\Filament\Resources\MasterProducts\Pages\ListMasterProducts;
use App\Models\Product;
use BackedEnum;
use Filament\Actions;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use UnitEnum;

class MasterProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Produk';

    protected static UnitEnum|string|null $navigationGroup = null;

    protected static ?string $modelLabel = 'Produk';

    protected static ?string $pluralModelLabel = 'Produk';

    protected static ?string $slug = 'master-products';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Left Column (2/3 width)
                Group::make()
                    ->schema([
                        Section::make('Product Information')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($set, ?string $state) => $set('slug', Str::slug($state))),

                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Product::class, 'slug', ignoreRecord: true),

                                Select::make('category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('name')->required(),
                                        TextInput::make('slug')->required(),
                                    ]),

                                TextInput::make('sku.sku')
                                    ->label('SKU')
                                    ->maxLength(50)
                                    ->placeholder('Will be auto-generated if left empty'),

                                RichEditor::make('description')
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'bold',
                                        'bulletList',
                                        'italic',
                                        'orderedList',
                                        'redo',
                                        'undo',
                                    ]),
                            ])
                            ->columns(2),

                        Section::make('Dimensions & Weight')
                            ->schema([
                                TextInput::make('sku.weight')
                                    ->label('Weight')
                                    ->numeric()
                                    ->suffix('g')
                                    ->placeholder('0'),

                                TextInput::make('sku.length')
                                    ->label('Length')
                                    ->numeric()
                                    ->suffix('cm')
                                    ->placeholder('0'),

                                TextInput::make('sku.width')
                                    ->label('Width')
                                    ->numeric()
                                    ->suffix('cm')
                                    ->placeholder('0'),

                                TextInput::make('sku.height')
                                    ->label('Height')
                                    ->numeric()
                                    ->suffix('cm')
                                    ->placeholder('0'),
                            ])
                            ->columns(4)
                            ->collapsible(),

                        Section::make('Attributes')
                            ->description('Product Variants (Size)')
                            ->schema([
                                Repeater::make('variants')
                                    ->relationship('variantsForRepeater')
                                    ->addActionLabel('Tambah Varian Produk')
                                    ->schema([
                                        TextInput::make('size')
                                            ->statePath('attributes.size')
                                            ->label('Size')
                                            ->required()
                                            ->dehydrated(fn($state) => $state !== null),

                                        TextInput::make('sku')
                                            ->label('SKU')
                                            ->maxLength(50),

                                        TextInput::make('unit_cost')
                                            ->label('Unit Cost')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0),

                                        TextInput::make('base_price')
                                            ->label('Base Price')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0),

                                        TextInput::make('selling_price')
                                            ->label('Selling Price')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0),

                                        TextInput::make('stock_quantity')
                                            ->label('Stock Quantity')
                                            ->numeric()
                                            ->readonly()
                                            ->default(0),

                                        FileUpload::make('image')
                                            ->statePath('attributes.image')
                                            ->label('Variant Image')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg'])
                                            ->maxSize(10240)
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('products/variants')
                                            ->helperText('Max 10MB. Auto convert to WebP.')
                                            ->imageResizeUpscale(false)
                                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $component): string {
                                                $filename = \Illuminate\Support\Str::random(20).'.webp';
                                                $image = Image::read($file->getRealPath())
                                                    ->cover(800, 800)
                                                    ->toWebp(90);
                                                $path = $component->getDirectory().'/'.$filename;
                                                Storage::disk($component->getDiskName())->put($path, (string) $image);
                                                return $path;
                                            })
                                            ->dehydrated(fn($state) => $state !== null),

                                        Toggle::make('is_active')
                                            ->label('Active')
                                            ->inline(false)
                                            ->default(true),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 2]),

                // Right Column (1/3 width)
                Group::make()
                    ->schema([
                        Section::make('Pricing & Stock')
                            ->schema([
                                TextInput::make('sku.unit_cost')
                                    ->label('Unit Cost (Harga Modal)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->placeholder('0'),

                                TextInput::make('sku.base_price')
                                    ->label('Base Price (Harga Coret)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->placeholder('0'),

                                TextInput::make('sku.selling_price')
                                    ->label('Selling Price (Harga Jual)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->placeholder('0'),

                                TextInput::make('sku.stock_quantity')
                                    ->label('Stock Quantity')
                                    ->numeric()
                                    ->readOnly()
                                    ->default(0)
                                    ->placeholder('0'),
                            ])
                            ->columns(2),

                        Section::make('Status')
                            ->schema([
                                Toggle::make('sku.is_active')
                                    ->label('Is Active')
                                    ->default(true)
                                    ->inline(false),

                                Toggle::make('is_featured')
                                    ->label('Is Featured')
                                    ->default(false)
                                    ->inline(false),
                                ])
                                ->columns(2),

                        Section::make('Image')
                            ->schema([
                                FileUpload::make('main_image')
                                    ->label('Main Product Image')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg'])
                                    ->maxSize(10240)
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('products')
                                    ->helperText('Ukuran file maksimal 10MB. Akan dikonversi ke WebP.')
                                    ->imageResizeUpscale(false)
                                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $component): string {
                                        $filename = \Illuminate\Support\Str::random(20).'.webp';
                                        $image = Image::read($file->getRealPath())
                                            ->cover(800, 800)
                                            ->toWebp(90);
                                        $path = $component->getDirectory().'/'.$filename;
                                        Storage::disk($component->getDiskName())->put($path, (string) $image);
                                        return $path;
                                    }),
                            ]),

                        Section::make('Product Gallery')
                            ->description('Upload hingga 5 gambar galeri produk')
                            ->schema([
                                Repeater::make('gallery_images')
                                    ->label('Gallery Images')
                                    ->schema([
                                        FileUpload::make('image_path')
                                            ->label('Gallery Image')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg'])
                                            ->maxSize(10240)
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('products/gallery')
                                            ->helperText('Max 10MB')
                                            ->imageResizeUpscale(false)
                                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $component): string {
                                                $filename = \Illuminate\Support\Str::random(20).'.webp';
                                                $image = Image::read($file->getRealPath())
                                                    ->cover(800, 800)
                                                    ->toWebp(90);
                                                $path = $component->getDirectory().'/'.$filename;
                                                Storage::disk($component->getDiskName())->put($path, (string) $image);
                                                return $path;
                                            })
                                            ->required(),
                                    ])
                                    ->addActionLabel('Tambah Gambar')
                                    ->reorderable()
                                    ->maxItems(5)
                                    ->grid(2)
                                    ->collapsible()
                                    ->collapsed(false)
                                    ->defaultItems(0)
                                    ->itemLabel(fn (array $state): ?string => !empty($state['image_path']) ? 'Gambar Galeri' : null),
                            ])
                            ->collapsible()
                            ->collapsed(false),
                            ])
                            ->columnSpan(['lg' => 1]),
                        ])
                        ->columns(3);

                    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('main_image')
                    ->label('Image')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl('https://placehold.co/100x100?text=No+Image'),

                TextColumn::make('name')
                    ->label('Product')
                    ->description(fn (Product $record): string => $record->category?->name ?? '-')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('sku.sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('SKU copied')
                    ->placeholder('-'),

                TextColumn::make('sku.base_price')
                    ->label('Price')
                    ->html()
                    ->formatStateUsing(function ($state, Product $record) {
                        $basePrice = $state;
                        $sellingPrice = $record->sku?->selling_price ?? 0;

                        $baseText = $basePrice
                            ? 'Rp '.number_format($basePrice, 0, ',', '.')
                            : '-';

                        $sellingText = 'Rp '.number_format($sellingPrice, 0, ',', '.');

                        return '
                            <div style="text-decoration: line-through; color: gray; font-size: 0.9em;">'.$baseText.'</div>
                            <div>'.$sellingText.'</div>
                        ';
                    })
                    ->sortable(),

                IconColumn::make('sku.is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('sku_active')
                    ->label('Active')
                    ->placeholder('All products')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('sku', fn (Builder $q) => $q->where('is_active', true)),
                        false: fn (Builder $query) => $query->whereHas('sku', fn (Builder $q) => $q->where('is_active', false)),
                    ),

                TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->placeholder('All products')
                    ->trueLabel('Featured')
                    ->falseLabel('Non-featured'),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->exports([
                        \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                            ->fromTable()
                            ->withFilename('master-products-'.date('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                            ->withColumns([
                                \pxlrbt\FilamentExcel\Columns\Column::make('id')
                                    ->heading('ID'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('name')
                                    ->heading('Product Name'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('slug')
                                    ->heading('Slug'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('category.name')
                                    ->heading('Category'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('description')
                                    ->heading('Description'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('sku.sku')
                                    ->heading('SKU'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('sku.unit_cost')
                                    ->heading('Unit Cost (Harga Modal)'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('sku.base_price')
                                    ->heading('Base Price (Harga Coret)'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('sku.selling_price')
                                    ->heading('Selling Price'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('sku.stock_quantity')
                                    ->heading('Stock Quantity'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('sku.weight')
                                    ->heading('Weight (g)'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('sku.length')
                                    ->heading('Length (cm)'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('sku.width')
                                    ->heading('Width (cm)'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('sku.height')
                                    ->heading('Height (cm)'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('sku.is_active')
                                    ->heading('SKU Active')
                                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('is_featured')
                                    ->heading('Is Featured')
                                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('created_at')
                                    ->heading('Created At'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('updated_at')
                                    ->heading('Updated At'),
                            ]),
                    ]),

                \Filament\Actions\ImportAction::make()
                    ->label('Import from Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('info')
                    ->importer(\App\Filament\Imports\MasterProductImporter::class)
                    ->successNotification(null),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMasterProducts::route('/'),
            'create' => CreateMasterProduct::route('/create'),
            'edit' => EditMasterProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['category', 'sku', 'variants'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
