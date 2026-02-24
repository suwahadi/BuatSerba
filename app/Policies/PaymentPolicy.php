<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('resource.payments.view_any') || $user->hasRole('admin');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo('resource.payments.view') || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('resource.payments.create') || $user->hasRole('admin');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo('resource.payments.update') || $user->hasRole('admin');
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo('resource.payments.delete') || $user->hasRole('admin');
    }

    public function restore(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo('resource.payments.restore') || $user->hasRole('admin');
    }

    public function forceDelete(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo('resource.payments.force_delete') || $user->hasRole('admin');
    }
}
