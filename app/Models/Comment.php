<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Post;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = [];
    // user who has commented

    public function author()
    {
      return $this->belongsTo(User::class, 'from_user');
    }

    // returns post of any comment
    public function post()
    {
      return $this->belongsTo(Post::class, 'on_post');
    }
}
