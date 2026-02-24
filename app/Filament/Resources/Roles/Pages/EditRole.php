<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $role = $this->getRecord();
        $permissions = $role->permissions->pluck('name')->toArray();
        
        $grouped = [];
        foreach ($permissions as $p) {
            $parts = explode('.', $p);
            $group = count($parts) >= 3 ? $parts[1] : 'general';
            $grouped['permissions_group_' . $group][] = $p;
        }

        return array_merge($data, $grouped);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $permissions = [];
        
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'permissions_group_') && is_array($value)) {
                $permissions = array_merge($permissions, $value);
                unset($data[$key]);
            }
        }

        $record->update($data);
        $record->syncPermissions($permissions);

        return $record;
    }
}
