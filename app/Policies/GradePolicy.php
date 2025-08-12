<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GradePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Grade $grade): bool
    {
        // Admin can view any grade
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can view grades they created
        if ($user->teacher && $grade->teacher_id === $user->teacher->id) {
            return true;
        }

        // Check if the grade belongs to a student in one of the teacher's groups
        if ($user->teacher) {
            $teacherGroupIds = $user->teacher->groups->pluck('id')->toArray();
            return in_array($grade->student->group_id, $teacherGroupIds);
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Both admin and teachers can create grades
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Grade $grade): bool
    {
        // Admin can update any grade
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can only update grades they created
        if ($user->teacher) {
            return $grade->teacher_id === $user->teacher->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Grade $grade): bool
    {
        // Admin can delete any grade
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can only delete grades they created
        if ($user->teacher) {
            return $grade->teacher_id === $user->teacher->id;
        }

        return false;
    }
}
