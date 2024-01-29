<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $rules = [
        'title' => ['required'],
        'body' => ['required'],
        'slug' => ['required']
    ];

    public function index()
    {
        $posts = Post::where('active', 1)->orderBy('updated_at', 'DESC')->paginate(10);
        return view('home')->with('posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if($request->user()->canPost()) {
            return view('post.create');
        }
        return Redirect::route('posts.index')->with('errors', 'Dont have permission to create post');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if($request->user()->canPost()){
            $this->validate($request, $this->rules);
            $input = Request::createFromGlobals()->all();
            $duplicate = Post::where('slug', $input['slug']/*$post->slug*/);
            if(!$duplicate){
                return Redirect::route('posts.store')->with('errors','Slug already exists.');
            }
            $input['author_id'] = $request->user()->id;
            if($request->has('save')) {
                $input['active'] = 0;
                $message = 'Post saved successfully';
                unset($input['save']);
            } else {
                $input['active'] = 1;
                $message = 'Post published successfully';
                unset($input['publish']);
            }
            Post::create($input);
            return Redirect::route('posts.edit', $input['slug'])->with('message', $message);
        }else {
            return Redirect::route('posts.index')->with('errors','Dont have permission to edit post');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post = Post::where('slug',$post->slug)->first();
        if($post) {
            if($post->active == 0) {
                return Redirect::route('posts.index')->with('errors', 'requested page not found 1 ');
            }
            $comments = $post->comments;
        } else {
            return Redirect::route('posts.index')->with('errors','requested page not found 2 ');
        }
        return view('post.show')->with('post', $post)->with('comments', $comments);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Post $post)
    {
        $post = Post::where('slug',$post->slug)->first();
        if($post && ($request->user()->id == $post->author_id || $request->user()->isAdmin())) {
            return view('post.edit')->with('post', $post);
        }else {
            return Redirect::route('posts.index')->with('errors', 'Dont have permission to edit post');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updatePost(Request $request)
    {
        $this->validate($request, $this->rules);
        $input = Request::createFromGlobals()->all();
        $post = Post::find($input['post_id']);
        unset($input['post_id']);
        if($post && ($post->author_id == $request->user()->id || $request->user()->isAdmin())) {
            if($request->has('save')) {
                $input['active'] = 0;
                $message = 'Post saved successfully';
                $landing = 'posts.edit';
                unset($input['save']);
            }else{
                $input['active'] = 1;
                $message = 'Post updated successfully';
                $landing = 'posts.show';
                unset($input['publish']);
            }
            $post->update($input);
            return Redirect::route($landing, $post->slug)->with('message', $message);
        }
        return Redirect::route('posts.index')->with('errors', 'Dont have permission to edit post');
        /*$post_id = $request->input('post_id');
        $post = Post::find($post_id);
        if($post && ($post->author_id == $request->user()->id || $request->user()->is_admin())) {
            $title = $request->input('title');
            $slug = $request->input('slug');
            $duplicate = Post::where('slug', $slug)->first();
            if ($duplicate) {
                if ($duplicate->id != $post_id) {
                    return Redirect::route('posts.edit', $post->slug);
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
            return Redirect::route($landing, $post->slug)->with('message', $message);
        }
        return Redirect::route($landing, $post->slug)->with('message', $message);*/
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $post = Post::find($id);
        if($post->author_id == $request->user()->id || $request->user()->isAdmin()){
            $post->delete();
            return Redirect::route('posts.index')->with('message', 'Post deleted.');
        }
        return Redirect::route('posts.index')->with('message', 'Dont have permission to delete post');
    }
}
