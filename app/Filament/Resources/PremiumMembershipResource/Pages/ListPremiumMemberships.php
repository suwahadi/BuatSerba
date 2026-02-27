<?php

namespace App\Filament\Resources\PremiumMembershipResource\Pages;

use App\Filament\Resources\PremiumMembershipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPremiumMemberships extends ListRecords
{
    protected static string $resource = PremiumMembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
