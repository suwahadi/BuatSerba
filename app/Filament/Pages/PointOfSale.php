<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Auth;

class PointOfSale extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected string $view = 'filament.pages.point-of-sale';

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected static ?string $navigationLabel = 'Kasir (POS)';

    protected static ?string $title = 'Kasir (POS)';

    protected static ?string $slug = 'pos';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem Internal';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('page.point_of_sales.access') || Auth::user()->hasRole('admin'));
    }
}
