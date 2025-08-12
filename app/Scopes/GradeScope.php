<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class GradeScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();
        
        if (!$user || $user->isAdmin()) {
            return;
        }

        // إذا كان المستخدم معلماً، نقوم بتصفية الدرجات حسب مجموعاته
        if ($user->hasRole('teacher')) {
            $teacherGroups = $user->teacher->groups()->pluck('id');
            $builder->whereIn('group_id', $teacherGroups);
            return;
        }

        // إذا كان المستخدم طالباً، نقوم بتصفية درجاته فقط
        if ($user->hasRole('student')) {
            $builder->where('student_id', $user->student->id);
            return;
        }

        // لباقي المستخدمين، نقوم بتصفية الدرجات حسب الفرع
        $builder->whereHas('student', function ($query) use ($user) {
            $query->where('branch_id', $user->branch_id);
        });
    }
}
