<?php

namespace App\Filament\Resources\InternalSaleResource\Pages;

use App\Filament\Resources\InternalSaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInternalSales extends ListRecords
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
