<?php

namespace App\Filament\Resources\ProductReviews;

use App\Filament\Resources\ProductReviews\Pages\ManageProductReviews;
use App\Models\ProductReview;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use UnitEnum;

class ProductReviewResource extends Resource
{
    protected static ?string $model = ProductReview::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static ?string $navigationLabel = 'Review';

    protected static UnitEnum|string|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 8;

    protected static ?string $slug = 'reviews';

    protected static ?string $modelLabel = 'Review Produk';

    protected static ?string $pluralModelLabel = 'Review Produk';

    protected static ?string $recordTitleAttribute = 'id';

    public static function canAccess(): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('resource.reviews.view_any') || Auth::user()->hasRole('admin'));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Review')
                    ->schema([
                        Placeholder::make('product_display')
                            ->label('Produk')
                            ->content(fn ($record) => $record?->product?->name ?? '-'),
                        Placeholder::make('user_display')
                            ->label('Pelanggan')
                            ->content(fn ($record) => $record?->user?->name ?? '-'),
                        Placeholder::make('order_display')
                            ->label('Nomor Pesanan')
                            ->content(fn ($record) => $record?->order?->order_number ?? '-'),
                        Placeholder::make('rating_display')
                            ->label('Rating')
                            ->content(fn ($record) => $record ? "{$record->rating} / 5" : '-'),
                        Textarea::make('review')
                            ->label('Ulasan')
                            ->readOnly()
                            ->columnSpanFull(),
                        Placeholder::make('images_display')
                            ->label('Gambar')
                            ->columnSpanFull()
                            ->content(function ($record) {
                                if (! $record || empty($record->images)) {
                                    return new HtmlString('<span class="text-gray-400 italic">Tidak ada gambar</span>');
                                }
                                $items = collect($record->images)->map(function ($path) {
                                    $url = Storage::url($path);

                                    return "<a href=\"{$url}\" target=\"_blank\" class=\"block\"><img src=\"{$url}\" alt=\"Review\" class=\"w-full aspect-square object-cover rounded-lg border border-gray-200 hover:opacity-80 transition\"></a>";
                                })->join('');

                                return new HtmlString("<div class=\"grid grid-cols-3 gap-3\">{$items}</div>");
                            }),
                        Placeholder::make('is_verified_display')
                            ->label('Pembelian Terverifikasi')
                            ->content(fn ($record) => $record?->is_verified_purchase ? 'Ya' : 'Tidak'),
                    ])
                    ->columns(2),

                Section::make('Status')
                    ->schema([
                        Toggle::make('is_approved')
                            ->label('Disetujui')
                            ->inline(false),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable()
                    ->url(fn (ProductReview $record) => $record->product ? url("/product/{$record->product->slug}") : null)
                    ->openUrlInNewTab()
                    ->limit(30),
                TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rating')
                    ->label('Rating & Review')
                    ->formatStateUsing(function ($state, ProductReview $record) {
                        $rating = (int) ($record->rating ?? 0);
                        $style = match ($rating) {
                            5, 4 => 'background-color:#dcfce7;color:#166534',
                            3, 2 => 'background-color:#fef9c3;color:#854d0e',
                            1 => 'background-color:#fee2e2;color:#991b1b',
                            default => 'background-color:#f3f4f6;color:#1f2937',
                        };
                        $badge = "<span class=\"inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium\" style=\"{$style}\">{$rating} / 5</span>";
                        $review = \Illuminate\Support\Str::limit($record->review ?? '', 50);

                        return new HtmlString($badge.'<br><span class="text-sm mt-1 block">'.e($review).'</span>');
                    })
                    ->html()
                    ->wrap(),
                IconColumn::make('is_verified_purchase')
                    ->label('Terverifikasi')
                    ->boolean(),
                ToggleColumn::make('is_approved')
                    ->label('Disetujui'),
            ])
            ->filters([
                SelectFilter::make('rating')
                    ->label('Rating')
                    ->options([
                        1 => '1 Bintang',
                        2 => '2 Bintang',
                        3 => '3 Bintang',
                        4 => '4 Bintang',
                        5 => '5 Bintang',
                    ]),
                TernaryFilter::make('is_approved')
                    ->label('Status Persetujuan')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Disetujui')
                    ->falseLabel('Belum Disetujui')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detail Review'),
                DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProductReviews::route('/'),
        ];
    }
}
