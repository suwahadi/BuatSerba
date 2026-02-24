<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('resource.master_products.view_any') || $user->hasRole('admin');
    }

    public function view(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('resource.master_products.view') || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('resource.master_products.create') || $user->hasRole('admin');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('resource.master_products.update') || $user->hasRole('admin');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('resource.master_products.delete') || $user->hasRole('admin');
    }

    public function restore(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('resource.master_products.restore') || $user->hasRole('admin');
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('resource.master_products.force_delete') || $user->hasRole('admin');
    }
}
