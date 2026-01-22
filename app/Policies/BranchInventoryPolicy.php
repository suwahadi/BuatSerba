<?php

namespace App\Policies;

use App\Models\BranchInventory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BranchInventoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('warehouse');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BranchInventory $branchInventory): bool
    {
        return $user->hasRole('warehouse');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('warehouse');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BranchInventory $branchInventory): bool
    {
        return $user->hasRole('warehouse');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BranchInventory $branchInventory): bool
    {
        return $user->hasRole('warehouse');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BranchInventory $branchInventory): bool
    {
        return $user->hasRole('warehouse');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BranchInventory $branchInventory): bool
    {
        return $user->hasRole('warehouse');
    }
}
