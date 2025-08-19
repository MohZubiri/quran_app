<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPlan extends Model
{
    protected $fillable = ['student_id', 'saving_from', 'saving_to', 'review_from', 'review_to','month'];

    public function student()
    {
        return $this->belongsTo(Student::class,'student_id', 'id');

    }
}
