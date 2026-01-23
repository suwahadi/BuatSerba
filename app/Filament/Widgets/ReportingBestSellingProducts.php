<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportingBestSellingProducts extends BaseWidget
{
    public ?array $reportFilter = [];

    protected function getDateRange(): array
    {
        $period = $this->reportFilter['period'] ?? 'month';
        
        return match ($period) {
            'today' => [Carbon::today(), Carbon::today()->endOfDay()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'custom' => [
                isset($this->reportFilter['start_date']) ? Carbon::parse($this->reportFilter['start_date']) : Carbon::now()->startOfMonth(),
                isset($this->reportFilter['end_date']) ? Carbon::parse($this->reportFilter['end_date'])->endOfDay() : Carbon::now()->endOfMonth()
            ],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }

    public function mount(array $reportFilter = []): void
    {
        $this->reportFilter = $reportFilter;
    }

    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = '20 Produk Terlaris';

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
                    ->orderBy('total_qty', 'desc')
                    ->limit(20)
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
                    ->label('Harga Modal')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('selling_price')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_profit')
                    ->label('Keuntungan')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
