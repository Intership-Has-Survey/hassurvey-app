<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\DetailPenjualan;
use App\Models\User;

class DetailPenjualanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any DetailPenjualan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DetailPenjualan $detailpenjualan): bool
    {
        return $user->checkPermissionTo('view DetailPenjualan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create DetailPenjualan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DetailPenjualan $detailpenjualan): bool
    {
        return $user->checkPermissionTo('update DetailPenjualan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DetailPenjualan $detailpenjualan): bool
    {
        return $user->checkPermissionTo('delete DetailPenjualan');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any DetailPenjualan');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DetailPenjualan $detailpenjualan): bool
    {
        return $user->checkPermissionTo('restore DetailPenjualan');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any DetailPenjualan');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, DetailPenjualan $detailpenjualan): bool
    {
        return $user->checkPermissionTo('replicate DetailPenjualan');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder DetailPenjualan');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DetailPenjualan $detailpenjualan): bool
    {
        return $user->checkPermissionTo('force-delete DetailPenjualan');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any DetailPenjualan');
    }
}
