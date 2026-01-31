<?php

namespace App\Filament\Resources\InternalSaleResource\Pages;

use App\Filament\Resources\InternalSaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageInternalSales extends ManageRecords
{
    protected static string $resource = InternalSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data'),
        ];
    }
}
