<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function handleRecordCreation(array $data): Role
    {
        $permissions = [];
        
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'permissions_group_') && is_array($value)) {
                $permissions = array_merge($permissions, $value);
                unset($data[$key]);
            }
        }

        $role = Role::create($data);
        $role->syncPermissions($permissions);

        return $role;
    }
}
