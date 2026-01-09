<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopProductsChart extends ChartWidget
{
    protected ?string $heading = '10 Produk Terlaris';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = OrderItem::select('product_name', DB::raw('sum(quantity) as total_sold'))
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Terjual',
                    'data' => $data->pluck('total_sold'),
                    'backgroundColor' => '#16a24a',
                    'borderColor' => '#16a24a',
                ],
            ],
            'labels' => $data->pluck('product_name'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
