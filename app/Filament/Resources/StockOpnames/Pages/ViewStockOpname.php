<?php

namespace App\Filament\Resources\StockOpnames\Pages;

use App\Filament\Resources\StockOpnames\StockOpnameResource;
use App\Models\Sku;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;

class ViewStockOpname extends ViewRecord
{
    protected static string $resource = StockOpnameResource::class;

    protected string $view = 'filament.resources.stock-opnames.pages.view-stock-opname';

    protected static ?string $title = 'Detail Stok Opname';

    public function adjustStock(): void
    {
        $record = $this->getRecord();

        if ($record->is_adjusted) {
            Notification::make()
                ->title('Stok sudah disesuaikan sebelumnya')
                ->warning()
                ->send();

            return;
        }

        DB::transaction(function () use ($record) {
            foreach ($record->items as $item) {
                $sku = Sku::find($item->sku_id);
                if ($sku) {
                    $sku->stock_quantity = $item->physical_stock;
                    $sku->save();

                    $item->new_system_stock = $item->physical_stock;
                    $item->is_adjusted = true;
                    $item->save();
                }
            }

            $record->is_adjusted = true;
            $record->adjusted_at = now();
            $record->save();
        });

        Notification::make()
            ->title('Berhasil Sinkronisasi Stok')
            ->success()
            ->send();

        $this->redirect(StockOpnameResource::getUrl('view', ['record' => $record->id]));
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        if ($record->is_adjusted) {
            return [];
        }

        return [
            Action::make('adjust')
                ->label('Sesuaikan Stok')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Sesuaikan Stok')
                ->modalDescription(
                    new \Illuminate\Support\HtmlString('Pastikan sistem sedang tidak melakukan transaksi apapun, karena proses ini akan mempengaruhi riwayat stok barang!<br><br>Apakah Anda yakin untuk sinkronisasi stok fisik dengan stok sistem?')
                )
                ->modalSubmitActionLabel('Ya')
                ->modalCancelActionLabel('Tidak')
                ->action(fn () => $this->adjustStock()),
        ];
    }
}
