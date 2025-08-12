<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class AttendanceScope implements Scope
{
    /**
     * تطبيق نطاق التصفية على الاستعلام.
     */
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();
        
        if ($user) {
            if ($user->hasRole('teacher')) {
                // المدرس يرى فقط حضور طلاب مجموعاته
                $builder->whereHas('student.group', function($query) use ($user) {
                    $query->where('teacher_id', $user->teacher->id);
                });
            } elseif (!$user->hasRole('super_admin')) {
                // مدير الفرع يرى فقط حضور طلاب فروعه
                $builder->whereHas('student.group.branch', function($query) use ($user) {
                    $query->whereIn('id', $user->branch->pluck('id'));
                });
            }
        }
    }
}
