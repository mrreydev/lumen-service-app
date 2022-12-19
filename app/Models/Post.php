<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = array('title', 'content', 'status', 'user_id');

    /**
     * Relation with Users
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation with Comments
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
