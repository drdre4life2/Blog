<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Str;
use App\Interfaces\PostServiceInterface;


class PostService implements PostServiceInterface {

    public function createPost($input){
        $post = new Post();
        $post->title = $input->get('title');
        $post->body = $input->get('body');
        $post->slug = Str::slug($post->title);

        return $post;
    }


}
