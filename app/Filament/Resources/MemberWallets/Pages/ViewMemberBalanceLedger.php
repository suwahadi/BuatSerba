<?php

namespace App\Filament\Resources\MemberWallets\Pages;

use App\Filament\Resources\MemberWallets\MemberBalanceLedgerResource;
use Filament\Resources\Pages\ManageRecords;

class ViewMemberBalanceLedger extends ManageRecords
{
    protected static string $resource = MemberBalanceLedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
