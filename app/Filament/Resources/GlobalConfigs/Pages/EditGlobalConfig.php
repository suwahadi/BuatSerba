<?php

namespace App\Filament\Resources\GlobalConfigs\Pages;

use App\Filament\Resources\GlobalConfigs\GlobalConfigResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGlobalConfig extends EditRecord
{
    protected static string $resource = GlobalConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
