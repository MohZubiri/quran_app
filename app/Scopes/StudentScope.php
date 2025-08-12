<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Models\Group;

class StudentScope implements Scope
{
    /**
     * تطبيق نطاق التصفية على الاستعلام.
     */
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();
        
        if ($user) {
            if ($user->hasRole('teacher')) {
               
                // المدرس يرى طلاب جميع مجموعاته من خلال التسجيلات
                $teacherGroups = Group::where('teacher_id', $user->teacher->id)->pluck('id');
                
                // تصفية الطلاب بناءً على التسجيلات النشطة في مجموعات المعلم
                $builder->where(function($query) use ($teacherGroups) {
                    // الطلاب الذين لديهم تسجيلات نشطة في مجموعات المعلم
                    $query->whereHas('enrollments', function($q) use ($teacherGroups) {
                        $q->whereIn('students.group_id', $teacherGroups)
                          ->where('status', 'active');
                    })
                    // أو الطلاب الذين تم تعيين مجموعتهم مباشرة إلى إحدى مجموعات المعلم
                    ->orWhereIn('students.group_id', $teacherGroups);
                });
        
                   
            } elseif (!$user->hasRole('super_admin')) {
                // مدير الفرع يرى فقط طلاب فروعه
                $builder ->whereIn('students.branch_id', $user->branch->pluck('id'))
                       ->select('students.*');
            }
        }
    }
}
