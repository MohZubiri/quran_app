<?php

namespace App\Models;

use App\Scopes\TeacherScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'qualifications',
        'specialization',
        'bio',
        'join_date',
        'branch_id',
        'status',
        'user_id',
    ];

    protected $dates = [
        'join_date',
    ];

    protected $casts = [
        'join_date' => 'date',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::addGlobalScope(new TeacherScope);
    }

    /**
     * Get the user associated with the teacher.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the branch that the teacher belongs to.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the groups that the teacher is assigned to.
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Get all groups taught by the teacher.
     */
    public function groupsTaught(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Get the attendance records created by the teacher.
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the grades given by the teacher.
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Get the progress logs created by the teacher.
     */
    public function progressLogs(): HasMany
    {
        return $this->hasMany(ProgressLog::class);
    }

    /**
     * Get all students in the teacher's groups through a hasManyThrough relationship.
     */
    public function students()
    {
        return $this->hasManyThrough(
            Student::class,
            Group::class,
            'teacher_id', // Foreign key on groups table...
            'group_id',   // Foreign key on students table...
            'id',         // Local key on teachers table...
            'id'         // Local key on groups table...
        );
    }
}
