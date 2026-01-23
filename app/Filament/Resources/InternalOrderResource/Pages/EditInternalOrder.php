<?php

namespace App\Filament\Resources\InternalOrderResource\Pages;

use App\Filament\Resources\InternalOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInternalOrder extends EditRecord
{
    protected static string $resource = InternalOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
