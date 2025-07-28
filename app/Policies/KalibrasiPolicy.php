<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Kalibrasi;
use App\Models\User;

class KalibrasiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Kalibrasi');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Kalibrasi $kalibrasi): bool
    {
        return $user->checkPermissionTo('view Kalibrasi');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Kalibrasi');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Kalibrasi $kalibrasi): bool
    {
        return $user->checkPermissionTo('update Kalibrasi');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Kalibrasi $kalibrasi): bool
    {
        return $user->checkPermissionTo('delete Kalibrasi');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Kalibrasi');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Kalibrasi $kalibrasi): bool
    {
        return $user->checkPermissionTo('restore Kalibrasi');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Kalibrasi');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Kalibrasi $kalibrasi): bool
    {
        return $user->checkPermissionTo('replicate Kalibrasi');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Kalibrasi');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Kalibrasi $kalibrasi): bool
    {
        return $user->checkPermissionTo('force-delete Kalibrasi');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Kalibrasi');
    }
}
