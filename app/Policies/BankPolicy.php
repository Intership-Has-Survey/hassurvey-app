<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Bank;
use App\Models\User;

class BankPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Bank');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bank $bank): bool
    {
        return $user->checkPermissionTo('view Bank');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Bank');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bank $bank): bool
    {
        return $user->checkPermissionTo('update Bank');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bank $bank): bool
    {
        return $user->checkPermissionTo('delete Bank');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Bank');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bank $bank): bool
    {
        return $user->checkPermissionTo('restore Bank');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Bank');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Bank $bank): bool
    {
        return $user->checkPermissionTo('replicate Bank');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Bank');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bank $bank): bool
    {
        return $user->checkPermissionTo('force-delete Bank');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Bank');
    }
}
