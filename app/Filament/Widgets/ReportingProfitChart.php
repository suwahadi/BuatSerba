<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ReportingProfitChart extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->hasPermissionTo('widget.reporting_profit_charts.access') || auth()->user()?->hasRole('admin') ?? false;
    }

    protected function getDateRange(): array
    {
        return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
    }

    protected ?array $reportFilter = ['period' => 'month'];

    protected ?string $heading = 'Grafik Keuntungan';

    protected ?string $maxHeight = '400px';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getDateRange();
        $period = $this->reportFilter['period'] ?? 'month';

        $groupBy = match ($period) {
            'today' => 'hour',
            'week', 'month' => 'day',
            'year' => 'month',
            'custom' => $startDate->diffInDays($endDate) > 60 ? 'month' : 'day',
            default => 'day',
        };

        $dateFormat = match ($groupBy) {
            'hour' => '%H:00',
            'day' => '%Y-%m-%d',
            'month' => '%Y-%m',
        };

        $dateSelect = "DATE_FORMAT(orders.created_at, '$dateFormat')";

        $data = Order::query()
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('skus', 'order_items.sku_id', '=', 'skus.id')
            ->where('orders.payment_status', 'paid')
            ->whereDate('orders.created_at', '>=', $startDate)
            ->whereDate('orders.created_at', '<=', $endDate)
            ->selectRaw("$dateSelect as date_group")
            ->selectRaw('SUM((order_items.price - COALESCE(skus.unit_cost, 0)) * order_items.quantity) as profit')
            ->groupBy('date_group')
            ->orderBy('date_group')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Profit (Keuntungan)',
                    'data' => $data->pluck('profit')->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $data->pluck('date_group')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
