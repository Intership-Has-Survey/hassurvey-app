<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\JenisPekerjaan;
use App\Models\User;

class JenisPekerjaanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any JenisPekerjaan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JenisPekerjaan $jenispekerjaan): bool
    {
        return $user->checkPermissionTo('view JenisPekerjaan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create JenisPekerjaan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JenisPekerjaan $jenispekerjaan): bool
    {
        return $user->checkPermissionTo('update JenisPekerjaan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JenisPekerjaan $jenispekerjaan): bool
    {
        return $user->checkPermissionTo('delete JenisPekerjaan');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any JenisPekerjaan');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JenisPekerjaan $jenispekerjaan): bool
    {
        return $user->checkPermissionTo('restore JenisPekerjaan');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any JenisPekerjaan');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, JenisPekerjaan $jenispekerjaan): bool
    {
        return $user->checkPermissionTo('replicate JenisPekerjaan');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder JenisPekerjaan');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JenisPekerjaan $jenispekerjaan): bool
    {
        return $user->checkPermissionTo('force-delete JenisPekerjaan');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any JenisPekerjaan');
    }
}
