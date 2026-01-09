<?php

namespace App\Filament\Resources\GlobalConfigs\Pages;

use App\Filament\Resources\GlobalConfigs\GlobalConfigResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGlobalConfigs extends ListRecords
{
    protected static string $resource = GlobalConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
