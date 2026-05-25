<?php

namespace App\Filament\Resources\FlashSales\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FlashSalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Sesi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tagline')
                    ->label('Tagline')
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('phase')
                    ->label('Status')
                    ->badge()
                    ->state(function ($record) {
                        $now = now();
                        if (! $record->is_active) {
                            return 'NON-AKTIF';
                        }
                        if ($record->starts_at > $now) {
                            return 'UPCOMING';
                        }
                        if ($record->ends_at < $now) {
                            return 'ENDED';
                        }

                        return 'LIVE';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'LIVE' => 'success',
                        'UPCOMING' => 'warning',
                        'ENDED' => 'gray',
                        default => 'danger',
                    }),

                TextColumn::make('starts_at')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('Berakhir')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('items_count')
                    ->label('Item')
                    ->counts('items')
                    ->badge()
                    ->color('info'),

                TextColumn::make('sort')
                    ->label('Urutan')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueLabel('Aktif')
                    ->falseLabel('Non-aktif')
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make(),
                ReplicateAction::make()
                    ->label('Duplikat')
                    ->excludeAttributes(['slug'])
                    ->beforeReplicaSaved(function ($replica) {
                        $replica->name = $replica->name.' (Copy)';
                        $replica->slug = null;
                        $replica->is_active = false;
                    })
                    ->after(function ($record, $replica) {
                        foreach ($record->items as $item) {
                            $replica->items()->create([
                                'sku_id' => $item->sku_id,
                                'flash_price' => $item->flash_price,
                                'original_price_snapshot' => $item->original_price_snapshot,
                                'stock_limit' => $item->stock_limit,
                                'sold_count' => 0,
                                'sort' => $item->sort,
                            ]);
                        }
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
            ])
            ->defaultSort('sort', 'asc');
    }
}
