<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('resource.orders.view_any') || $user->hasRole('admin');
    }

    public function view(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('resource.orders.view') || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('resource.orders.create') || $user->hasRole('admin');
    }

    public function update(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('resource.orders.update') || $user->hasRole('admin');
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('resource.orders.delete') || $user->hasRole('admin');
    }

    public function restore(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('resource.orders.restore') || $user->hasRole('admin');
    }

    public function forceDelete(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('resource.orders.force_delete') || $user->hasRole('admin');
    }
}
