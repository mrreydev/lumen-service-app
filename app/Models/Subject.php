<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';
    protected $fillable = array('name', 'professor_id');

    public $timestamps = true;

    /**
     * * Relation to Students
     */
    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }
}
