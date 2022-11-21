<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Subject;

class Student extends Model
{
    protected $table = 'students';
    protected $fillable = array('nim', 'name', 'major_id');

    public $timestamps = true;
    protected $hidden = array('major_id');

    /**
     * * Relation to Majors
     */
    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    /**
     * * Relation to Scores
     */
    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
