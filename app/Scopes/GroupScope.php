<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class GroupScope implements Scope
{
    /**
     * تطبيق نطاق التصفية على الاستعلام.
     */
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();
        
        if ($user) {
            if ($user->hasRole('teacher')) {
                // المدرس يرى فقط مجموعاته DD($user->teacher);
              
                $builder->where('teacher_id', $user->teacher->id);
            } elseif (!$user->hasRole('super_admin')) {
                // مدير الفرع يرى فقط مجموعات فروعه
                $builder->where('branch_id', $user->branch_id);
            }
        }
    }
}
