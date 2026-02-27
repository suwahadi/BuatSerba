<?php

namespace App\Filament\Resources\PremiumMembershipResource\Pages;

use App\Filament\Resources\PremiumMembershipResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePremiumMembership extends CreateRecord
{
    protected static string $resource = PremiumMembershipResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
