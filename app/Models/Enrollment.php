<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Student;
use App\Models\Group;
use App\Scopes\EnrollmentScope;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'group_id',
        'branch_id',
        'enrollment_date',
        'status',
        'notes',
    ];

    protected $dates = [
        'enrollment_date',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::addGlobalScope(new EnrollmentScope);
    }

    /**
     * Get the student that belongs to the enrollment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the group that belongs to the enrollment.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
