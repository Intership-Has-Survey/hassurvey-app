<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\JenisAlat;
use App\Models\User;

class JenisAlatPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any JenisAlat');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JenisAlat $jenisalat): bool
    {
        return $user->checkPermissionTo('view JenisAlat');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create JenisAlat');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JenisAlat $jenisalat): bool
    {
        return $user->checkPermissionTo('update JenisAlat');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JenisAlat $jenisalat): bool
    {
        return $user->checkPermissionTo('delete JenisAlat');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any JenisAlat');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JenisAlat $jenisalat): bool
    {
        return $user->checkPermissionTo('restore JenisAlat');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any JenisAlat');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, JenisAlat $jenisalat): bool
    {
        return $user->checkPermissionTo('replicate JenisAlat');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder JenisAlat');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JenisAlat $jenisalat): bool
    {
        return $user->checkPermissionTo('force-delete JenisAlat');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any JenisAlat');
    }
}
