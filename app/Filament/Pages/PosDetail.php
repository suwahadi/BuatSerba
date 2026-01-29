<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Auth;

class PosDetail extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected string $view = 'filament.pages.pos-detail';

    protected static bool $shouldRegisterNavigation = false;

    public ?Order $order = null;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected static ?string $title = 'Detail Transaksi';

    protected static ?string $slug = 'pos/{orderNumber}';

    public function mount(string $orderNumber): void
    {
        $this->order = Order::with(['items.product', 'user'])
            ->where('order_number', $orderNumber)
            ->where('payment_method', 'cash')
            ->firstOrFail();
    }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole(['admin', 'finance']);
    }
}
