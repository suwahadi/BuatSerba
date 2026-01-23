<?php

namespace App\Filament\Pages\Reporting;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Order;
use App\Models\Sku;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class Stocks extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected string $view = 'filament.pages.reporting.stocks';

    protected static string|\UnitEnum|null $navigationGroup = 'Reporting';

    protected static ?string $navigationLabel = 'Laporan Stok';

    protected static ?string $slug = 'reporting/stocks';

    protected static ?string $title = 'Laporan Stok';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole(['admin', 'warehouse']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sku::query()
                    ->with(['product', 'product.category'])
                    ->addSelect(['last_sold_date' => Order::select('paid_at')
                        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                        ->whereColumn('order_items.sku_id', 'skus.id')
                        ->where('orders.payment_status', 'paid')
                        ->latest('paid_at')
                        ->limit(1)
                    ])
            )
            ->columns([
                Tables\Columns\ImageColumn::make('product.main_image')
                    ->label('Gambar')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl('https://placehold.co/100x100?text=No+Image'),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(false),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Nama Produk')
                    ->formatStateUsing(function ($state, $record) {
                        $categoryName = $record->product->category->name ?? '-';
                        return new HtmlString($state . '<br><small style="color: #555">' . $categoryName . '</small>');
                    }),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Sisa Stok')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state > 100 => 'success',
                        $state >= 20 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('last_sold_date')
                    ->label('Terakhir Update')
                    ->dateTime('d F Y H:i', 'Asia/Jakarta'),
            ])
            ->defaultSort('stock_quantity', 'desc')
            ->actions([
                \Filament\Actions\Action::make('Lihat')
                    ->label('Lihat')
                    ->button()
                    ->url(fn (Sku $record) => \App\Filament\Resources\MasterProducts\MasterProductResource::getUrl('edit', ['record' => $record->product_id])),
            ])
            ->paginated(true);
    }
}
