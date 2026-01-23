<?php

namespace App\Filament\Resources\Vouchers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use Filament\Tables\Columns\ImageColumn;

class VouchersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl('https://placehold.co/100x100?text=No+Image'),

                \Filament\Tables\Columns\TextColumn::make('voucher_code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->label('Code')
                    ->badge()
                    ->color('success'),

                \Filament\Tables\Columns\TextColumn::make('voucher_name')
                    ->searchable()
                    ->sortable()
                    ->label('Name'),

                \Filament\Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'percentage' => 'Percentage',
                        'fixed' => 'Nominal',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => $state === 'percentage' ? 'info' : 'success'),

                \Filament\Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(function ($record) {
                        if ($record->type === 'percentage') {
                            return $record->amount.'%';
                        }

                        return 'Rp '.number_format($record->amount, 0, ',', '.');
                    }),
                    
                \Filament\Tables\Columns\TextColumn::make('min_spend')
                    ->label('Min Spend')
                    ->money('IDR')
                    ->toggleable(),

                \Filament\Tables\Columns\TextColumn::make('usage_summary')
                    ->label('Usage')
                    ->state(fn ($record) => $record->usage_count . ' / ' . ($record->usage_limit ?? '∞'))
                    ->badge()
                    ->color(fn ($record) => ($record->usage_limit && $record->usage_count >= $record->usage_limit) ? 'danger' : 'gray'),

                \Filament\Tables\Columns\TextColumn::make('valid_period')
                    ->label('Validity')
                    ->state(fn ($record) => 
                        ($record->valid_start ? \Carbon\Carbon::parse($record->valid_start)->translatedFormat('d M Y H:i') : '-') . 
                        '<br>' . 
                        ($record->valid_end ? \Carbon\Carbon::parse($record->valid_end)->translatedFormat('d M Y H:i') : '-')
                    )
                    ->html(),

                \Filament\Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'fixed' => 'Nominal',
                        'percentage' => 'Percentage',
                    ])
                    ->label('Type'),

                \Filament\Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->native(false),

                \Filament\Tables\Filters\TernaryFilter::make('is_free_shipment')
                    ->label('Free Shipping')
                    ->boolean()
                    ->trueLabel('Free shipping')
                    ->falseLabel('No free shipping')
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort', 'asc');
    }
}
