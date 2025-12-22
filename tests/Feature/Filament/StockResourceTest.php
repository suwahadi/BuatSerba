<?php

declare(strict_types=1);

use App\Filament\Resources\Stocks\StockResource;

test('stock resource exists', function () {
    expect(StockResource::class)->toBeString();
    expect(StockResource::getModel())->toBe(\App\Models\BranchInventory::class);
});

test('stock resource has correct slug', function () {
    expect(StockResource::getSlug())->toBe('stocks');
});
