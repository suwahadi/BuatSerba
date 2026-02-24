<?php

namespace App\Filament\Pages\Reporting;

use App\Models\OrderItem;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

class Order extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected string $view = 'filament.pages.reporting.order';

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Penjualan';

    protected static ?string $slug = 'reporting/order';

    protected static ?string $title = 'Laporan Penjualan';

    protected static ?int $navigationSort = 1;

    #[Url(as: 'filters')]
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('page.orders.access') || Auth::user()->hasRole('admin'));
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
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderItem::query()
                    ->fromSub(function ($query) {
                        [$start, $end] = $this->getDateRange();
                        $query->from('order_items')
                            ->join('orders', 'order_items.order_id', '=', 'orders.id')
                            ->leftJoin('skus', 'order_items.sku_id', '=', 'skus.id')
                            ->select(
                                DB::raw('MAX(order_items.id) as id'),
                                'order_items.product_id',
                                'order_items.product_name',
                                'order_items.sku_code',
                                DB::raw('MAX(skus.unit_cost) as cost_price'),
                                DB::raw('MAX(skus.selling_price) as selling_price'),
                                DB::raw('SUM(order_items.quantity) as total_qty'),
                                DB::raw('SUM((order_items.price - COALESCE(skus.unit_cost, 0)) * order_items.quantity) as total_profit')
                            )
                            ->where('orders.payment_status', 'paid')
                            ->whereBetween('orders.created_at', [$start, $end])
                            ->groupBy('order_items.product_id', 'order_items.product_name', 'order_items.sku_code');
                    }, 'order_items')
            )
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Produk')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku_code')
                    ->label('SKU')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_qty')
                    ->label('Terjual')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_price')
                    ->label('Modal')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('selling_price')
                    ->label('Jual')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_profit')
                    ->label('Laba')
                    ->money('IDR')
                    ->sortable()
                    ->summarize(Sum::make()->label('Grand Total')->money('IDR', locale: 'id')),
            ])
            ->defaultSort('total_profit', 'desc')
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->exports([
                        \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                            ->fromTable()
                            ->withFilename('laporan-penjualan-'.date('Y-m-d_His'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                            ->withColumns([
                                \pxlrbt\FilamentExcel\Columns\Column::make('product_name')
                                    ->heading('Produk'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('sku_code')
                                    ->heading('SKU'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('total_qty')
                                    ->heading('Terjual'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('cost_price')
                                    ->heading('Modal'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('selling_price')
                                    ->heading('Jual'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('total_profit')
                                    ->heading('Laba'),
                            ]),
                    ]),
            ]);
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
}
