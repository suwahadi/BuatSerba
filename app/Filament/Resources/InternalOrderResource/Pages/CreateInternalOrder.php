<?php

namespace App\Filament\Resources\InternalOrderResource\Pages;

use App\Filament\Resources\InternalOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInternalOrder extends CreateRecord
{
    protected static string $resource = InternalOrderResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
