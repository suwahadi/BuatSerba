<?php

namespace App\Filament\Resources\PremiumMembershipResource\Pages;

use App\Filament\Resources\PremiumMembershipResource;
use Filament\Resources\Pages\ManageRecords;

class ManagePremiumMemberships extends ManageRecords
{
    protected static string $resource = PremiumMembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 
        ];
    }
}