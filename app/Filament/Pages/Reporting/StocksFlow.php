<?php

namespace App\Filament\Pages\Reporting;

use App\Models\StockMovement;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Url;

class StocksFlow extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected string $view = 'filament.pages.reporting.stocks-flow';

    protected static string|\UnitEnum|null $navigationGroup = 'Reporting';

    protected static ?string $navigationLabel = 'Laporan Arus Stok';

    protected static ?string $slug = 'reporting/stocks-flow';

    protected static ?string $title = 'Laporan Arus Stok';

    protected static ?int $navigationSort = 4;

    #[Url(as: 'filters')]
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole(['admin', 'warehouse']);
    }

    public function mount(): void
    {
        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('search')
                            ->label('Cari SKU / Produk')
                            ->placeholder('Ketik untuk mencari...')
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn () => $this->redirect(static::getUrl(['filters' => $this->data]))),

                        Select::make('period')
                            ->label('Periode')
                            ->options([
                                'today' => 'Hari Ini',
                                'week' => 'Minggu Ini',
                                'last_week' => 'Minggu Lalu',
                                'month' => 'Bulan Ini',
                                'last_month' => 'Bulan Lalu',
                                'year' => 'Tahun Ini',
                                'last_year' => 'Tahun Lalu',
                                'custom' => 'Range Tanggal',
                            ])
                            ->default('')
                            ->disableOptionWhen(fn (string $value): bool => $value === '')
                            ->live()
                            ->afterStateUpdated(fn () => $this->redirect(static::getUrl(['filters' => $this->data]))),

                        DatePicker::make('start_date')
                            ->label('Dari Tanggal')
                            ->visible(fn (Get $get) => $get('period') === 'custom')
                            ->live()
                            ->afterStateUpdated(fn () => $this->redirect(static::getUrl(['filters' => $this->data]))),

                        DatePicker::make('end_date')
                            ->label('Sampai Tanggal')
                            ->visible(fn (Get $get) => $get('period') === 'custom')
                            ->live()
                            ->afterStateUpdated(fn () => $this->redirect(static::getUrl(['filters' => $this->data]))),
                    ])
                    ->columns(4),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getStockMovementsQuery())
            ->columns([
                ImageColumn::make('product_image')
                    ->label('Gambar')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl('https://placehold.co/100x100?text=No+Image'),

                TextColumn::make('sku_code')
                    ->label('Produk')
                    ->formatStateUsing(fn ($record) => new HtmlString(
                        '<strong>'.e($record->sku_code ?? '-').'</strong><br><small style="color: #666">'.e($record->product_name ?? '-').'</small>'
                    )),

                TextColumn::make('stock_in')
                    ->label('Masuk')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => $state > 0 ? '+'.$state : '0'),

                TextColumn::make('stock_out')
                    ->label('Keluar')
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(fn ($state) => $state > 0 ? '-'.$state : '0'),

                TextColumn::make('current_stock')
                    ->label('Stok Saat Ini')
                    ->badge()
                    ->color(fn ($state) => $state > 20 ? 'success' : ($state > 0 ? 'warning' : 'danger')),

                TextColumn::make('last_update')
                    ->label('Update Terakhir')
                    ->dateTime('d M Y H:i:s'),
            ])
            ->paginated([10, 25, 50]);
    }

    protected function getStockMovementsQuery(): Builder
    {
        $dateRange = $this->getDateRange();
        $startDate = $dateRange[0] ?? null;
        $endDate = $dateRange[1] ?? null;
        $search = $this->data['search'] ?? '';

        $orderItemsQuery = DB::table('order_items')
            ->select([
                'order_items.sku_code',
                'order_items.product_name',
                'order_items.sku_id',
                'products.main_image as product_image',
                DB::raw('0 as stock_in'),
                'order_items.quantity as stock_out',
                'orders.paid_at as transaction_date',
            ])
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.payment_status', 'paid')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('order_items.sku_code', 'like', '%'.$search.'%')
                        ->orWhere('order_items.product_name', 'like', '%'.$search.'%');
                });
            })
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('orders.paid_at', [$startDate, $endDate]);
            });

        $stockOpnameQuery = DB::table('stock_opname_items')
            ->select([
                'skus.sku as sku_code',
                'products.name as product_name',
                'stock_opname_items.sku_id',
                'products.main_image as product_image',
                DB::raw('CASE WHEN stock_opname_items.difference > 0 THEN stock_opname_items.difference ELSE 0 END as stock_in'),
                DB::raw('CASE WHEN stock_opname_items.difference < 0 THEN ABS(stock_opname_items.difference) ELSE 0 END as stock_out'),
                'stock_opnames.adjusted_at as transaction_date',
            ])
            ->join('stock_opnames', 'stock_opname_items.stock_opname_id', '=', 'stock_opnames.id')
            ->join('skus', 'stock_opname_items.sku_id', '=', 'skus.id')
            ->leftJoin('products', 'skus.product_id', '=', 'products.id')
            ->where('stock_opnames.is_adjusted', true)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('skus.sku', 'like', '%'.$search.'%')
                        ->orWhere('products.name', 'like', '%'.$search.'%');
                });
            })
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('stock_opnames.adjusted_at', [$startDate, $endDate]);
            });

        $unionQuery = $orderItemsQuery->unionAll($stockOpnameQuery);

        return StockMovement::query()
            ->fromSub(
                DB::table(DB::raw("({$unionQuery->toSql()}) as combined"))
                    ->mergeBindings($unionQuery)
                    ->select([
                        DB::raw('MIN(sku_id) as id'),
                        'sku_code',
                        DB::raw('MAX(product_name) as product_name'),
                        DB::raw('MAX(product_image) as product_image'),
                        DB::raw('MAX(sku_id) as sku_id'),
                        DB::raw('SUM(stock_in) as stock_in'),
                        DB::raw('SUM(stock_out) as stock_out'),
                        DB::raw('MAX(transaction_date) as last_update'),
                    ])
                    ->groupBy('sku_code'),
                'grouped_movements'
            )
            ->select([
                'grouped_movements.*',
                'skus.stock_quantity as current_stock',
            ])
            ->leftJoin('skus', 'grouped_movements.sku_id', '=', 'skus.id')
            ->orderBy('stock_out', 'desc');
    }

    protected function getDateRange(): array
    {
        $period = $this->data['period'] ?? 'month';

        return match ($period) {
            'today' => [Carbon::today(), Carbon::today()->endOfDay()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'last_week' => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'last_month' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'last_year' => [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()],
            'custom' => [
                isset($this->data['start_date']) ? Carbon::parse($this->data['start_date']) : Carbon::now()->startOfMonth(),
                isset($this->data['end_date']) ? Carbon::parse($this->data['end_date'])->endOfDay() : Carbon::now()->endOfMonth(),
            ],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }

    public function getBreadcrumbs(): array
    {
        return [
            Stocks::getUrl() => 'Laporan Stok',
            '' => 'Arus Stok',
        ];
    }
}
