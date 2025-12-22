<?php

declare(strict_types=1);

use App\Filament\Resources\Payments\PaymentResource;

test('payment resource exists', function () {
    expect(PaymentResource::class)->toBeString();
    expect(PaymentResource::getModel())->toBe(\App\Models\Payment::class);
});

test('payment resource has correct slug', function () {
    expect(PaymentResource::getSlug())->toBe('payments');
});

test('payment resource cannot create new records', function () {
    expect(PaymentResource::canCreate())->toBeFalse();
});
