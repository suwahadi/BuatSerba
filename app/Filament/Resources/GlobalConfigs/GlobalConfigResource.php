<?php

namespace App\Filament\Resources\GlobalConfigs;

use App\Filament\Resources\GlobalConfigs\Pages\ManageGlobalConfigs;
use App\Filament\Resources\GlobalConfigs\Schemas\GlobalConfigForm;
use App\Filament\Resources\GlobalConfigs\Tables\GlobalConfigsTable;
use App\Models\GlobalConfig;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use UnitEnum;

class GlobalConfigResource extends Resource
{
    protected static ?string $model = GlobalConfig::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static UnitEnum|string|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 12;

    public static function form(Schema $schema): Schema
    {
        return GlobalConfigForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GlobalConfigsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGlobalConfigs::route('/'),
        ];
    }
}
