<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profiles';
    protected $fillable = array('user_id', 'gender', 'birth_date', 'address');

    public $timestamps = true;

    /**
     * * Relation to Users
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
