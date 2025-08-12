<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Models\User;

class BranchScope implements Scope
{
    /**
     * تطبيق نطاق التصفية على الاستعلام.
     */
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();
        
        // المشرف يرى جميع الفروع
        if ($user && !$user->hasRole('super_admin')) {
            // المستخدم العادي يرى فقط الفرع المرتبط به
            $builder->where('id', $user->branch_id);
        }
    }
}
