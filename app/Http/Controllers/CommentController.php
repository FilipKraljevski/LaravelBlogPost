<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $input['from_user'] = $request->user()->id;
        $input['on_post'] = $request->input('on_post');
        $input['body'] = $request->input('body');
        $slug = $request->input('slug');
        Comment::create($input);
        return Redirect::route('posts.show', $slug)->with('message', 'Comment published');
    }

    /*public function destroy(Request $request, Comment $comment)
    {
        if($post->author_id == $request->user()->id || $request->user()->isAdmin()){
            $post->delete();
            return Redirect::route('posts.index')->with('message', 'Post deleted.');
        }
        $comment->delete();
        return Redirect::route('posts.index')->with('message', 'Comment deleted.');
    }*/
}
