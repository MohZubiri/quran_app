<?php

namespace App\Rules;

use App\Models\Branch;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class UserBranchRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = Auth::user();
        
        // إذا كان المستخدم مدير نظام، يمكنه اختيار أي فرع
        if ($user->hasRole('super_admin')) {
            return;
        }
        // التحقق من أن الفرع المختار هو نفس فرع المستخدم المصرح له
        if ((int)$value !== (int)$user->branch_id) {
            $fail('لا يمكنك اختيار فرع غير الفرع المخصص لك.');
        }
    }
}
