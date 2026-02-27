<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print_invoice')
                ->label('Invoice')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('orders.print-invoice', ['order' => $this->getRecord()->id]) . '?auto=1')
                ->openUrlInNewTab(),
            Actions\Action::make('print_awb')
                ->label('Resi')
                ->icon('heroicon-o-qr-code')
                ->url(fn () => route('orders.print-awb', ['order' => $this->getRecord()->id]) . '?auto=1')
                ->openUrlInNewTab(),
        ];
    }
}
