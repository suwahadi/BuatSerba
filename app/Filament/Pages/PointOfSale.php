<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Auth;

class PointOfSale extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected string $view = 'filament.pages.point-of-sale';

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected static ?string $navigationLabel = 'Point of Sale';

    protected static ?string $title = 'Point of Sale';

    protected static ?string $slug = 'pos';

    protected static string|\UnitEnum|null $navigationGroup = 'Internal Management';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole(['admin', 'finance']);
    }
}
