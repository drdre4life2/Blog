<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    // posts has many comments
    // returns all comments on that post
    public function comments()
    {
      return $this->hasMany(Comment::class, 'on_post');
    }

    // returns the instance of the user who is author of that post
    public function author()
    {
      return $this->belongsTo(User::class, 'author_id');
    }

}
