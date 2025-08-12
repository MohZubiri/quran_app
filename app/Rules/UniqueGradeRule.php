<?php

namespace App\Rules;

use App\Models\Grade;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueGradeRule implements ValidationRule
{
    protected $studentId;
    protected $subjectId;
    protected $teacherId;
    protected $date;
    protected $gradeType;

    public function __construct($studentId, $subjectId, $teacherId, $date, $gradeType)
    {
        $this->studentId = $studentId;
        $this->subjectId = $subjectId;
        $this->teacherId = $teacherId;
        $this->date = $date;
        $this->gradeType = $gradeType;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = Grade::where('student_id', $this->studentId)
            ->where('subject_id', $this->subjectId)
            ->where('teacher_id', $this->teacherId)
            ->whereDate('date', $this->date)
            ->where('grade_type', $this->gradeType)
            ->exists();

        if ($exists) {
            $fail('يوجد تقييم مسجل مسبقاً لهذا الطالب في نفس اليوم لنفس المادة والمدرس.');
        }
    }
}
