<?php

namespace App\Filament\Resources\PaymentConfirmations\Pages;

use App\Filament\Resources\PaymentConfirmations\PaymentConfirmationResource;
use Filament\Resources\Pages\ManageRecords;

class ManagePaymentConfirmations extends ManageRecords
{
    protected static string $resource = PaymentConfirmationResource::class;
}
