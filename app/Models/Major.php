<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $table = 'majors';
    protected $fillable = array('name');

    public $timestamps = true;

    /**
     * * Relation to Students
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
