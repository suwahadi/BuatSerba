<?php

namespace App\Filament\Resources\MasterProducts\Pages;

use App\Filament\Resources\MasterProducts\MasterProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterProducts extends ListRecords
{
    protected static string $resource = MasterProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Data'),
        ];
    }
}
