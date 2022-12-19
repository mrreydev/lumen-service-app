<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    protected $fillable = array('body', 'user_id', 'post_id');

    /**
     * Relation with Users
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation with Posts
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
