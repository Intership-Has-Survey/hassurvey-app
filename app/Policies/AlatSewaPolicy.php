<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\AlatSewa;
use App\Models\User;

class AlatSewaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any AlatSewa');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AlatSewa $alatsewa): bool
    {
        return $user->checkPermissionTo('view AlatSewa');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create AlatSewa');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AlatSewa $alatsewa): bool
    {
        return $user->checkPermissionTo('update AlatSewa');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AlatSewa $alatsewa): bool
    {
        return $user->checkPermissionTo('delete AlatSewa');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any AlatSewa');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AlatSewa $alatsewa): bool
    {
        return $user->checkPermissionTo('restore AlatSewa');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any AlatSewa');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, AlatSewa $alatsewa): bool
    {
        return $user->checkPermissionTo('replicate AlatSewa');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder AlatSewa');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AlatSewa $alatsewa): bool
    {
        return $user->checkPermissionTo('force-delete AlatSewa');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any AlatSewa');
    }
}
