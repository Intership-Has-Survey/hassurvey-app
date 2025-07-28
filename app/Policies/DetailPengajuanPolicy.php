<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\DetailPengajuan;
use App\Models\User;

class DetailPengajuanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any DetailPengajuan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DetailPengajuan $detailpengajuan): bool
    {
        return $user->checkPermissionTo('view DetailPengajuan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create DetailPengajuan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DetailPengajuan $detailpengajuan): bool
    {
        return $user->checkPermissionTo('update DetailPengajuan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DetailPengajuan $detailpengajuan): bool
    {
        return $user->checkPermissionTo('delete DetailPengajuan');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any DetailPengajuan');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DetailPengajuan $detailpengajuan): bool
    {
        return $user->checkPermissionTo('restore DetailPengajuan');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any DetailPengajuan');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, DetailPengajuan $detailpengajuan): bool
    {
        return $user->checkPermissionTo('replicate DetailPengajuan');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder DetailPengajuan');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DetailPengajuan $detailpengajuan): bool
    {
        return $user->checkPermissionTo('force-delete DetailPengajuan');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any DetailPengajuan');
    }
}
