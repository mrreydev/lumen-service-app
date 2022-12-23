<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = array('name', 'slug');
    
    /**
     * Posts that belongs to Category
     */
    public function posts() 
    {
        return $this->belongsToMany(Post::class);
    }
}
