<?php

namespace App\Filament\Resources\MemberWallets\Pages;

use App\Filament\Resources\MemberWallets\MemberWalletResource;
use Filament\Resources\Pages\ManageRecords;

class ManageMemberWallets extends ManageRecords
{
    protected static string $resource = MemberWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
