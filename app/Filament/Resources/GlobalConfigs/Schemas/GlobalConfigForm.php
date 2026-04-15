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

                        \Filament\Forms\Components\TextInput::make('value')
                            ->required()
                            ->label('Value')
                            ->columnSpanFull()
                            ->numeric(fn ($record) => in_array($record?->key, ['cashback', 'premium_membership_price', 'maintenance_mode', 'sort']))
                            ->minValue(fn ($record) => in_array($record?->key, ['cashback', 'premium_membership_price', 'maintenance_mode', 'sort']) ? 0 : null)
                            ->maxValue(fn ($record) => match($record?->key) {
                                'cashback' => 100,
                                default => null,
                            })
                            ->suffix(fn ($record) => $record && $record->key === 'cashback' ? '%' : null)
                            ->prefix(fn ($record) => $record && $record->key === 'premium_membership_price' ? 'Rp' : null)
                            ->helperText(fn ($record) => match($record?->key) {
                                'cashback' => 'Masukkan angka persentase cashback (0-100)',
                                'premium_membership_price' => 'Masukkan harga (dalam Rupiah)',
                                'maintenance_mode' => '1 = Aktif, 0 = Nonaktif',
                                'sort' => 'Urutan tampilan',
                                'site_name' => 'Nama website',
                                'tracking_code_header' => 'Code tracking (GTM, GA, Meta Pixel, dll)',
                                default => null,
                            })
                            ->placeholder(fn ($record) => match($record?->key) {
                                'cashback' => 'Contoh: 5',
                                'premium_membership_price' => 'Contoh: 100000',
                                'maintenance_mode' => '0 atau 1',
                                'site_name' => 'Contoh: BuatSerba.com',
                                'tracking_code_header' => 'Contoh: <script>...</script>',
                                default => 'Masukkan value',
                            }),

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
