<?php

namespace App\Models;

use App\Scopes\StudentScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'birth_date',
        'parent_phone',
        'branch_id',
        'group_id',
        'user_id',
        'notes'
    ];

    protected $dates = [
        'birth_date'
    ];

    protected $casts = [
        'birth_date' => 'date'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::addGlobalScope(new StudentScope);
    }
   public function studentPlan()
    {
        return $this->hasOne(studentPlan::class, 'student_id', 'id');
    }
    /**
     * Get the user associated with the student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the branch that the student belongs to.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the group that the student belongs to.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the enrollments for the student.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the groups for the student through enrollments.
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'enrollments')
            ->withPivot('enrollment_date', 'end_date', 'status', 'notes')
            ->withTimestamps();
    }

    /**
     * Get the active group for the student.
     */
    public function activeGroup()
    {
        return $this->belongsToMany(Group::class, 'enrollments')
                    ->wherePivot('status', 'active')
                    ->withTimestamps()
                    ->first();
    }

    /**
     * Get the attendance records for the student.
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the student's attendance records.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the grades for the student.
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Get the progress logs for the student.
     */
    public function progressLogs(): HasMany
    {
        return $this->hasMany(ProgressLog::class);
    }
}
