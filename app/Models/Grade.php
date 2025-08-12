<?php

namespace App\Models;

use App\Scopes\GradeScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'subject_id',
        'group_id',
        'branch_id',
        'date',
        'grade',
        'grade_type',
        'notes',
        'verses_covered',
    ];

    protected $dates = [
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::addGlobalScope(new GradeScope);
    }

    /**
     * Get the student that belongs to the grade.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the teacher that created the grade.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the subject that belongs to the grade.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the group that belongs to the grade.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
    
    /**
     * Get the branch that belongs to the grade.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
