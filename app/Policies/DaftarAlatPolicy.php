<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\DaftarAlat;
use App\Models\User;

class DaftarAlatPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any DaftarAlat');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DaftarAlat $daftaralat): bool
    {
        return $user->checkPermissionTo('view DaftarAlat');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create DaftarAlat');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DaftarAlat $daftaralat): bool
    {
        return $user->checkPermissionTo('update DaftarAlat');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DaftarAlat $daftaralat): bool
    {
        return $user->checkPermissionTo('delete DaftarAlat');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any DaftarAlat');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DaftarAlat $daftaralat): bool
    {
        return $user->checkPermissionTo('restore DaftarAlat');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any DaftarAlat');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, DaftarAlat $daftaralat): bool
    {
        return $user->checkPermissionTo('replicate DaftarAlat');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder DaftarAlat');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DaftarAlat $daftaralat): bool
    {
        return $user->checkPermissionTo('force-delete DaftarAlat');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any DaftarAlat');
    }
}
