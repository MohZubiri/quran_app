<?php

namespace App\Models;

use App\Scopes\ProgressLogScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'date',
        'surah',
        'from_verse',
        'to_verse',
        'type', // حفظ، مراجعة، تلاوة
        'evaluation',
        'notes'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
       // static::addGlobalScope(new ProgressLogScope);
    }

    /**
     * Get the student that belongs to the progress log.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the subject that belongs to the progress log.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the teacher that created the progress log.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the branch that the progress log belongs to.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
