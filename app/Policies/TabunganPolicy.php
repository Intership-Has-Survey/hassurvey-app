<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Tabungan;
use App\Models\User;

class TabunganPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Tabungan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tabungan $tabungan): bool
    {
        return $user->checkPermissionTo('view Tabungan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Tabungan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tabungan $tabungan): bool
    {
        return $user->checkPermissionTo('update Tabungan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tabungan $tabungan): bool
    {
        return $user->checkPermissionTo('delete Tabungan');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Tabungan');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tabungan $tabungan): bool
    {
        return $user->checkPermissionTo('restore Tabungan');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Tabungan');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Tabungan $tabungan): bool
    {
        return $user->checkPermissionTo('replicate Tabungan');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Tabungan');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tabungan $tabungan): bool
    {
        return $user->checkPermissionTo('force-delete Tabungan');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Tabungan');
    }
}
