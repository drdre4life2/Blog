<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidatePostRequest;
use App\Interfaces\PostServiceInterface;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\PostService;

class PostController extends Controller
{
    private $postService;

    public function __construct(PostServiceInterface $service)
    {
        $this->postService = $service;
    }

    public function index()
    {

        $posts = Post::where('active', 1)->orderBy('created_at', 'desc')->paginate(5);
        $title = 'Latest Posts';
        return view('home', compact('posts', 'title'));
    }

    public function create(Request $request)
    {
        //
        if ($request->user()->can_post()) {
            return view('posts.create');
        } else {
            return redirect('/')->withErrors('You do not have sufficient permissions to write post');
        }
    }

    public function store(ValidatePostRequest $request)
    {

        $post = $this->postService->createPost($request);
        $duplicate = Post::where('slug', $post->slug)->first();
        if ($duplicate) {
            return redirect('new-post')->withErrors('Title already exists.')->withInput();
        }

        $post->author_id = $request->user()->id;
        if ($request->has('save')) {
            $post->active = 0;
            $message = 'Post saved successfully';
        } else {
            $post->active = 1;
            $message = 'Post published successfully';
        }
        $post->save();
        return redirect('edit/' . $post->slug)->withMessage($message);
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->first();
        if (!$post) {
            return redirect('/')->withErrors('requested page not found');
        }
        $comments = $post->comments;
        return view('posts.show', compact('post', 'comments'));
    }

    public function edit(Request $request, $slug)
    {
        $post = Post::where('slug', $slug)->first();
        if ($post && ($request->user()->id == $post->author_id || $request->user()->is_admin())) {
            return view('posts.edit')->with('post', $post);
        }

        return redirect('/')->withErrors('you do not have sufficient permissions');
    }

    public function update(Request $request)
    {
      //
      $post_id = $request->input('post_id');
      $post = Post::find($post_id);
      if ($post && ($post->author_id == $request->user()->id || $request->user()->is_admin())) {
        $title = $request->input('title');
        $slug = Str::slug($title);
        $duplicate = Post::where('slug', $slug)->first();
        if ($duplicate) {
          if ($duplicate->id != $post_id) {
            return redirect('edit/' . $post->slug)->withErrors('Title already exists.')->withInput();
          } else {
            $post->slug = $slug;
          }
        }

        $post->title = $title;
        $post->body = $request->input('body');

        if ($request->has('save')) {
          $post->active = 0;
          $message = 'Post saved successfully';
          $landing = 'edit/' . $post->slug;
        } else {
          $post->active = 1;
          $message = 'Post updated successfully';
          $landing = $post->slug;
        }
        $post->save();
        return redirect($landing)->withMessage($message);
      } else {
        return redirect('/')->withErrors('you do not have sufficient permissions');
      }
    }

    public function destroy(Request $request, $id)
  {
    //
    $post = Post::find($id);
    if($post && ($post->author_id == $request->user()->id || $request->user()->is_admin()))
    {
      $post->delete();
      $data['message'] = 'Post deleted Successfully';
    }
    else
    {
      $data['errors'] = 'Invalid Operation. You do not have sufficient permissions';
    }
    return redirect('/')->with($data);
  }
}
