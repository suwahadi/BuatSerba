<?php

declare(strict_types=1);

use App\Filament\Pages\Reporting\StocksFlow;

test('stocks flow page class exists', function () {
    expect(StocksFlow::class)->toBeString();
    expect(StocksFlow::getSlug())->toBe('reporting/stocks-flow');
});

test('stocks flow page is registered in navigation', function () {
    expect(StocksFlow::shouldRegisterNavigation())->toBeTrue();
});

test('stocks flow page has navigation sort 4', function () {
    $reflection = new ReflectionClass(StocksFlow::class);
    $property = $reflection->getProperty('navigationSort');
    $property->setAccessible(true);
    expect($property->getValue())->toBe(4);
});

test('stocks flow page has table method', function () {
    $page = new StocksFlow;
    expect(method_exists($page, 'table'))->toBeTrue();
});

test('stocks flow page has getBreadcrumbs method', function () {
    $page = new StocksFlow;
    expect(method_exists($page, 'getBreadcrumbs'))->toBeTrue();
});

test('stocks flow page has form method', function () {
    $page = new StocksFlow;
    expect(method_exists($page, 'form'))->toBeTrue();
});

test('stocks flow page has data property for filters', function () {
    $page = new StocksFlow;
    expect(property_exists($page, 'data'))->toBeTrue();
});

test('stocks flow page has getDateRange method', function () {
    $page = new StocksFlow;
    expect(method_exists($page, 'getDateRange'))->toBeTrue();
});
