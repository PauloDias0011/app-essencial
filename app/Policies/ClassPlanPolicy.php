<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\ClassPlan;
use App\Models\User;

class ClassPlanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any ClassPlan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ClassPlan $classplan): bool
    {
        return $user->checkPermissionTo('view ClassPlan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create ClassPlan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ClassPlan $classplan): bool
    {
        return $user->checkPermissionTo('update ClassPlan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ClassPlan $classplan): bool
    {
        return $user->checkPermissionTo('delete ClassPlan');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any ClassPlan');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ClassPlan $classplan): bool
    {
        return $user->checkPermissionTo('restore ClassPlan');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any ClassPlan');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, ClassPlan $classplan): bool
    {
        return $user->checkPermissionTo('replicate ClassPlan');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder ClassPlan');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ClassPlan $classplan): bool
    {
        return $user->checkPermissionTo('force-delete ClassPlan');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any ClassPlan');
    }
}
