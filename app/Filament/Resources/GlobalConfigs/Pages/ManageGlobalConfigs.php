<?php

namespace App\Filament\Resources\GlobalConfigs\Pages;

use App\Filament\Resources\GlobalConfigs\GlobalConfigResource;
use Filament\Resources\Pages\ManageRecords;

class ManageGlobalConfigs extends ManageRecords
{
    protected static string $resource = GlobalConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Create action disabled - configs are seeded
        ];
    }
}
