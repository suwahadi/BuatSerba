<?php

namespace App\Filament\Resources\GlobalConfigs\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Table;

class GlobalConfigsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('key')
                    ->sortable()
                    ->label('Key'),

                \Filament\Tables\Columns\TextColumn::make('value')
                    ->limit(50)
                    ->label('Value'),

                \Filament\Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->label('Description')
                    ->toggleable(),

                \Filament\Tables\Columns\TextColumn::make('sort')
                    ->sortable()
                    ->label('Sort'),

                \Filament\Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('sort', 'asc')
            ->defaultPaginationPageOption(50);
    }
}
