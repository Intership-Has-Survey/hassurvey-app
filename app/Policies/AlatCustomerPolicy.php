<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\AlatCustomer;
use App\Models\User;

class AlatCustomerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any AlatCustomer');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AlatCustomer $alatcustomer): bool
    {
        return $user->checkPermissionTo('view AlatCustomer');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create AlatCustomer');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AlatCustomer $alatcustomer): bool
    {
        return $user->checkPermissionTo('update AlatCustomer');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AlatCustomer $alatcustomer): bool
    {
        return $user->checkPermissionTo('delete AlatCustomer');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any AlatCustomer');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AlatCustomer $alatcustomer): bool
    {
        return $user->checkPermissionTo('restore AlatCustomer');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any AlatCustomer');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, AlatCustomer $alatcustomer): bool
    {
        return $user->checkPermissionTo('replicate AlatCustomer');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder AlatCustomer');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AlatCustomer $alatcustomer): bool
    {
        return $user->checkPermissionTo('force-delete AlatCustomer');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any AlatCustomer');
    }
}
