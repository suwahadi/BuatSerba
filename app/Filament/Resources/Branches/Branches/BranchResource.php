<?php

namespace App\Filament\Resources\Branches\Branches;

use App\Filament\Resources\Branches\Branches\Pages\ManageBranches;
use App\Models\Branch;
use App\Services\RajaongkirService;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $recordTitleAttribute = 'name';

    protected static UnitEnum|string|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Cabang';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Cabang';

    protected static ?string $pluralModelLabel = 'Cabang';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->maxLength(20)
                    ->unique(Branch::class, 'code', ignoreRecord: true)
                    ->label('Kode Cabang')
                    ->placeholder('Misal: JKT001'),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Cabang')
                    ->placeholder('Misal: Cabang Jakarta Pusat'),

                Select::make('province_id')
                    ->label('Provinsi')
                    ->required()
                    ->searchable()
                    ->live()
                    ->options(function () {
                        $rajaongkir = new RajaongkirService;
                        $provinces = $rajaongkir->getProvinces();

                        return collect($provinces)->mapWithKeys(function ($province) {
                            return [$province['id'] => $province['name']];
                        })->toArray();
                    })
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('city_id', null);
                        $set('district_id', null);
                        $set('subdistrict_id', null);
                        $set('city_name', null);
                        $set('district_name', null);
                        $set('subdistrict_name', null);

                        // Save province name
                        if ($state) {
                            $rajaongkir = new RajaongkirService;
                            $provinces = $rajaongkir->getProvinces();
                            $selectedProvince = collect($provinces)->firstWhere('id', $state);
                            if ($selectedProvince) {
                                $set('province_name', $selectedProvince['name']);
                            }
                        } else {
                            $set('province_name', null);
                        }
                    }),

                Select::make('city_id')
                    ->label('Kota / Kabupaten')
                    ->required()
                    ->searchable()
                    ->live()
                    ->options(function (Get $get) {
                        $provinceId = $get('province_id');
                        if (! $provinceId) {
                            return [];
                        }

                        $rajaongkir = new RajaongkirService;
                        $cities = $rajaongkir->getCities($provinceId);

                        return collect($cities)->mapWithKeys(function ($city) {
                            $type = isset($city['type']) ? trim($city['type']) : '';
                            $name = $type ? "{$type} {$city['name']}" : $city['name'];

                            return [$city['id'] => $name];
                        })->toArray();
                    })
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        $set('district_id', null);
                        $set('subdistrict_id', null);
                        $set('district_name', null);
                        $set('subdistrict_name', null);

                        // Save city name
                        $provinceId = $get('province_id');
                        if ($provinceId && $state) {
                            $rajaongkir = new RajaongkirService;
                            $cities = $rajaongkir->getCities($provinceId);
                            $selectedCity = collect($cities)->firstWhere('id', $state);
                            if ($selectedCity) {
                                $type = isset($selectedCity['type']) ? trim($selectedCity['type']) : '';
                                $name = $type ? "{$type} {$selectedCity['name']}" : $selectedCity['name'];
                                $set('city_name', $name);
                            }
                        } else {
                            $set('city_name', null);
                        }
                    }),

                Select::make('district_id')
                    ->label('Kecamatan')
                    ->required()
                    ->searchable()
                    ->live()
                    ->options(function (Get $get) {
                        $cityId = $get('city_id');
                        if (! $cityId) {
                            return [];
                        }

                        $rajaongkir = new RajaongkirService;
                        $districts = $rajaongkir->getDistricts($cityId);

                        return collect($districts)->mapWithKeys(function ($district) {
                            return [$district['id'] => $district['name']];
                        })->toArray();
                    })
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        $set('subdistrict_id', null);
                        $set('subdistrict_name', null);

                        // Get selected district name and save it
                        $cityId = $get('city_id');
                        if ($cityId && $state) {
                            $rajaongkir = new RajaongkirService;
                            $districts = $rajaongkir->getDistricts($cityId);
                            $selectedDistrict = collect($districts)->firstWhere('id', $state);

                            if ($selectedDistrict) {
                                $set('district_name', $selectedDistrict['name']);
                            }
                        } else {
                            $set('district_name', null);
                        }
                    }),

                Select::make('subdistrict_id')
                    ->label('Kelurahan')
                    ->required()
                    ->searchable()
                    ->live()
                    ->options(function (Get $get) {
                        $districtId = $get('district_id');
                        if (! $districtId) {
                            return [];
                        }

                        $rajaongkir = new RajaongkirService;
                        $subdistricts = $rajaongkir->getSubdistricts($districtId);

                        return collect($subdistricts)->mapWithKeys(function ($subdistrict) {
                            return [$subdistrict['id'] => $subdistrict['name']];
                        })->toArray();
                    })
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        // Get selected subdistrict name and save it
                        $districtId = $get('district_id');
                        if ($districtId && $state) {
                            $rajaongkir = new RajaongkirService;
                            $subdistricts = $rajaongkir->getSubdistricts($districtId);
                            $selectedSubdistrict = collect($subdistricts)->firstWhere('id', $state);

                            if ($selectedSubdistrict) {
                                $set('subdistrict_name', $selectedSubdistrict['name']);
                            }
                        } else {
                            $set('subdistrict_name', null);
                        }
                    }),

                TextInput::make('full_address')
                    ->maxLength(500)
                    ->columnSpanFull()
                    ->label('Alamat Lengkap'),

                TextInput::make('phone')
                    ->tel()
                    ->maxLength(20)
                    ->label('No. Telepon'),

                TextInput::make('email')
                    ->email()
                    ->maxLength(100)
                    ->label('Email'),

                // Hidden fields to store location names
                TextInput::make('province_name')
                    ->hidden()
                    ->dehydrated(),

                TextInput::make('city_name')
                    ->hidden()
                    ->dehydrated(),

                TextInput::make('district_name')
                    ->hidden()
                    ->dehydrated(),

                TextInput::make('subdistrict_name')
                    ->hidden()
                    ->dehydrated(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->label('Kode'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Cabang'),

                TextColumn::make('province_name')
                    ->label('Provinsi')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

                TextColumn::make('city_name')
                    ->label('Kota / Kab')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

                TextColumn::make('district_name')
                    ->label('Kecamatan')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

                TextColumn::make('subdistrict_name')
                    ->label('Kelurahan')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

                TextColumn::make('phone')
                    ->label('No. Telp')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('email')
                    ->label('Email')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                // No bulk actions
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBranches::route('/'),
        ];
    }
}
