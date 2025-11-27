<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\KategoriPengajuan;
use App\Models\User;

class KategoriPengajuanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any KategoriPengajuan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, KategoriPengajuan $kategoripengajuan): bool
    {
        return $user->checkPermissionTo('view KategoriPengajuan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create KategoriPengajuan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, KategoriPengajuan $kategoripengajuan): bool
    {
        return $user->checkPermissionTo('update KategoriPengajuan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, KategoriPengajuan $kategoripengajuan): bool
    {
        return $user->checkPermissionTo('delete KategoriPengajuan');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any KategoriPengajuan');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, KategoriPengajuan $kategoripengajuan): bool
    {
        return $user->checkPermissionTo('restore KategoriPengajuan');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any KategoriPengajuan');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, KategoriPengajuan $kategoripengajuan): bool
    {
        return $user->checkPermissionTo('replicate KategoriPengajuan');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder KategoriPengajuan');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, KategoriPengajuan $kategoripengajuan): bool
    {
        return $user->checkPermissionTo('force-delete KategoriPengajuan');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any KategoriPengajuan');
    }
}
