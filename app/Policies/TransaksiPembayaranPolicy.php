<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\TransaksiPembayaran;
use App\Models\User;

class TransaksiPembayaranPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any TransaksiPembayaran');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TransaksiPembayaran $transaksipembayaran): bool
    {
        return $user->checkPermissionTo('view TransaksiPembayaran');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create TransaksiPembayaran');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TransaksiPembayaran $transaksipembayaran): bool
    {
        return $user->checkPermissionTo('update TransaksiPembayaran');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TransaksiPembayaran $transaksipembayaran): bool
    {
        return $user->checkPermissionTo('delete TransaksiPembayaran');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any TransaksiPembayaran');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TransaksiPembayaran $transaksipembayaran): bool
    {
        return $user->checkPermissionTo('restore TransaksiPembayaran');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any TransaksiPembayaran');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, TransaksiPembayaran $transaksipembayaran): bool
    {
        return $user->checkPermissionTo('replicate TransaksiPembayaran');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder TransaksiPembayaran');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TransaksiPembayaran $transaksipembayaran): bool
    {
        return $user->checkPermissionTo('force-delete TransaksiPembayaran');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any TransaksiPembayaran');
    }
}
