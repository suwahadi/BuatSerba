<?php

namespace App\Filament\Resources\InternalSaleResource\Pages;

use App\Filament\Resources\InternalSaleResource;
use Filament\Resources\Pages\EditRecord;

class EditInternalSale extends EditRecord
{
    protected static string $resource = InternalSaleResource::class;

    public function getTitle(): string
    {
        return 'Ubah Data';
    }

}
