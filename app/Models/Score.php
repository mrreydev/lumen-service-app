<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $table = 'scores';
    protected $fillable = array('student_id', 'task_score', 'midterm_score', 'finals_score');

    public $timestamps = true;

    /**
     * * Relation to Students
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
