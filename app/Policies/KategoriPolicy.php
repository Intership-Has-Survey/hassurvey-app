<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Kategori;
use App\Models\User;

class KategoriPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Kategori');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Kategori $kategori): bool
    {
        return $user->checkPermissionTo('view Kategori');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Kategori');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Kategori $kategori): bool
    {
        return $user->checkPermissionTo('update Kategori');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Kategori $kategori): bool
    {
        return $user->checkPermissionTo('delete Kategori');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Kategori');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Kategori $kategori): bool
    {
        return $user->checkPermissionTo('restore Kategori');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Kategori');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Kategori $kategori): bool
    {
        return $user->checkPermissionTo('replicate Kategori');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Kategori');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Kategori $kategori): bool
    {
        return $user->checkPermissionTo('force-delete Kategori');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Kategori');
    }
}
