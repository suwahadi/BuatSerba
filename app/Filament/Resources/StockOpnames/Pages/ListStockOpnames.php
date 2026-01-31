<?php

namespace App\Filament\Resources\StockOpnames\Pages;

use App\Filament\Resources\StockOpnames\StockOpnameResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStockOpnames extends ListRecords
{
    protected static string $resource = StockOpnameResource::class;

    protected static ?string $title = 'Stok Opname';

    public function getSubheading(): ?\Illuminate\Contracts\Support\Htmlable
    {
        return new \Illuminate\Support\HtmlString('<span style="font-size: 14px; line-height: 1.2;">Aktivitas pencocokan jumlah stok barang yang ada di gudang (real) dengan yang tercatat di sistem</span>');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Data'),
        ];
    }
}
