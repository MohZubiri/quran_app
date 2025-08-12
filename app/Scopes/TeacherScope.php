<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TeacherScope implements Scope
{
    /**
     * تطبيق نطاق التصفية على الاستعلام.
     */
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();
        
        if ($user) {
            if ($user->hasRole('teacher')) {
              
                // المدرس يرى فقط بياناته
                $builder;
            } elseif (!$user->hasRole('super_admin')) {
                // مدير الفرع يرى فقط المدرسين في فروعه
                $builder->whereIn('teachers.branch_id', $user->branch->pluck('id'));
            }
        }
    }
}
