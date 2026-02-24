<?php

namespace App\Policies;

use App\Models\BranchInventory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BranchInventoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('resource.branches.view_any') || $user->hasRole('admin');
    }

    public function view(User $user, BranchInventory $branchInventory): bool
    {
        return $user->hasPermissionTo('resource.branches.view') || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('resource.branches.create') || $user->hasRole('admin');
    }

    public function update(User $user, BranchInventory $branchInventory): bool
    {
        return $user->hasPermissionTo('resource.branches.update') || $user->hasRole('admin');
    }

    public function delete(User $user, BranchInventory $branchInventory): bool
    {
        return $user->hasPermissionTo('resource.branches.delete') || $user->hasRole('admin');
    }

    public function restore(User $user, BranchInventory $branchInventory): bool
    {
        return $user->hasPermissionTo('resource.branches.restore') || $user->hasRole('admin');
    }

    public function forceDelete(User $user, BranchInventory $branchInventory): bool
    {
        return $user->hasPermissionTo('resource.branches.force_delete') || $user->hasRole('admin');
    }
}
