<?php

namespace App\Filament\Resources\InternalSaleResource\Pages;

use App\Filament\Resources\InternalSaleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInternalSale extends CreateRecord
{
    protected static string $resource = InternalSaleResource::class;

    public function getTitle(): string 
    {
        return 'Tambah Data';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function canCreateAnother(): bool
    {
        return false;
    }

}
