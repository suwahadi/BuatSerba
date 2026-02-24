<?php

namespace App\Filament\Resources\Roles;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Role';

    protected static UnitEnum|string|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 99;

    public static function canAccess(): bool
    {
        return Auth::check() && (Auth::user()->hasPermissionTo('resource.roles.view_any') || Auth::user()->hasRole('admin'));
    }

    public static function form(Schema $schema): Schema
    {
        $permissions = Permission::where('guard_name', 'web')->get();
        $grouped = [];

        // Actions to hide from UI
        $hiddenActions = ['restore', 'force_delete'];

        foreach ($permissions as $p) {
            $parts = explode('.', $p->name);
            if (count($parts) >= 3) {
                $group = $parts[1];
                $action = $parts[2];
            } else {
                $group = 'general';
                $action = $parts[0] ?? $p->name;
            }

            // Skip hidden actions
            if (in_array($action, $hiddenActions)) {
                continue;
            }

            $grouped[$group][$p->name] = self::humanizeAction($action);
        }

        $permissionSections = [];
        foreach ($grouped as $group => $options) {
            $permissionSections[] = Section::make(Str::headline($group))
                ->schema([
                    CheckboxList::make('permissions_group_' . $group)
                        ->hiddenLabel()
                        ->options($options)
                        ->columnSpanFull()
                        ->bulkToggleable(),
                ]);
        }

        return $schema
            ->schema([
                Section::make('Role Details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Role Name')
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('guard_name')
                            ->label('Guard')
                            ->default('web')
                            ->required(),
                    ])
                    ->columns(2)
                    ->extraAttributes(['class' => 'bg-gray-50']),

                Section::make('Permissions')
                    ->schema([
                        Grid::make(3)
                            ->schema($permissionSections)
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function humanizeAction(string $action): string
    {
        $map = [
            'view_any' => 'View Any',
            'view' => 'View',
            'create' => 'Create',
            'update' => 'Update',
            'delete' => 'Delete',
            // 'restore' => 'Restore',
            // 'force_delete' => 'Force Delete',
            'access' => 'Access',
        ];
        return $map[$action] ?? Str::headline(str_replace('_', ' ', $action));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissions')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
