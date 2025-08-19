<?php

namespace App\Models;

use App\Scopes\GroupScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject_id',
        'teacher_id',
        'schedule',
        'capacity',
        'status',
        'description',
        'branch_id',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::addGlobalScope(new GroupScope);
    }

    /**
     * Get the branch that the group belongs to.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the subject for the group.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the teacher for the group.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the enrollments for the group.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the students for the group through enrollments.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments')
            ->withPivot('enrollment_date', 'end_date', 'status', 'notes')
            ->withTimestamps();
    }

    /**
     * Get the attendance records for the group.
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the grades for the group.
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }
}
