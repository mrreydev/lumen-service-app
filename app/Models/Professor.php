<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    protected $table = 'professors';
    protected $fillable = array('nip', 'name');

    public $timestamps = true;

    public function subject()
    {
        return $this->hasOne(Subject::class);
    }
}
