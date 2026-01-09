<?php

namespace App\Filament\Resources\GlobalConfigs\Schemas;

use Filament\Schemas\Schema;

class GlobalConfigForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Global Config')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('key')
                            ->required()
                            ->maxLength(191)
                            ->unique(ignoreRecord: true)
                            ->label('Key')
                            ->readOnly()
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('sort')
                            ->numeric()
                            ->default(0)
                            ->label('Sort Order')
                            ->columnSpan(1),

                        \Filament\Forms\Components\Textarea::make('value')
                            ->required()
                            ->label('Value')
                            ->rows(3)
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
