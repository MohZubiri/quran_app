<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyPlan extends Model
{
    protected $fillable = ['plan_number', 'group_number', 'lessons_count', 'min_performance', 'status'];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_number', 'id');
    }
}
