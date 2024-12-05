<?php

namespace App\Policies;

use App\Models\StudentInactive;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudentInactivePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_student::inactive');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StudentInactive $studentInactive): bool
    {
        return $user->can('view_student::inactive');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_student::inactive');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StudentInactive $studentInactive): bool
    {
        return $user->can('update_student::inactive');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StudentInactive $studentInactive): bool
    {
        return $user->can('delete_student::inactive');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_student::inactive');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StudentInactive $studentInactive): bool
    {
        return $user->can('restore_student::inactive');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StudentInactive $studentInactive): bool
    {
        return $user->can('force_delete_student::inactive');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_student::inactive');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_student::inactive');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, StudentInactive $studentInactive): bool
    {
        return $user->can('replicate_student::inactive');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_student::inactive');
    }
}
