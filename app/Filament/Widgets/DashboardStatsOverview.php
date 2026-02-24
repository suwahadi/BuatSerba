<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class DashboardStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool 
    {
        return auth()->user()?->hasPermissionTo('widget.dashboard_stats_overviews.access') || auth()->user()?->hasRole('admin') ?? false;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Produk', Product::count())
                ->icon('heroicon-o-shopping-bag'),
            Stat::make('Total Order', Order::count())
                ->icon('heroicon-o-shopping-cart'),
            Stat::make('Total User', User::where('role', '!=', 'admin')->count())
                ->icon('heroicon-o-users'),
            Stat::make('Total Omset (Rp)', new HtmlString('<span class="text-sm font-bold">' . number_format(Order::where('payment_status', 'paid')->sum('total'), 0, ',', '.') . '</span>'))
                ->icon('heroicon-o-banknotes'),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4;
    }
}
