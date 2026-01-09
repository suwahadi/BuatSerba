<?php

namespace App\Filament\Resources\GlobalConfigs\Pages;

use App\Filament\Resources\GlobalConfigs\GlobalConfigResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGlobalConfig extends CreateRecord
{
    protected static string $resource = GlobalConfigResource::class;
}
