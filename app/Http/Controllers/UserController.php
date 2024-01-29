<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{

    public function getUserPosts($id){
        $posts = Post::where('author_id', $id)->where('active', 1)->orderBy('updated_at', 'DESC')->paginate(10);
        return view('home')->with('posts', $posts);
    }

    public function getAllUserPosts(Request $request)
    {
        $user = $request->user();
        $posts = Post::where('author_id',$user->id)->orderBy('updated_at', 'DESC')->paginate(10);
        return view('home')->with('posts', $posts);
    }

    public function getUserDraftPosts(Request $request)
    {
        $user = $request->user();
        $posts = Post::where('author_id',$user->id)->where('active','0')->orderBy('updated_at', 'DESC')->paginate(10);
        return view('home')->with('posts', $posts);
    }

    /**
     * profile for user
     */
    public function profile(Request $request, $id)
    {
        $data['user'] = User::find($id);
        if (!$data['user']) {
            return Redirect::route('posts.index');
        }
        if ($request -> user() && $data['user'] -> id == $request -> user() -> id) {
            $data['author'] = true;
        } else {
            $data['author'] = null;
        }
        $data['comments_count'] = $data['user']->comments->count();
        $data['posts_count'] = $data['user']->posts->count();
        $data['posts_active_count'] = $data['user']->posts->where('active', 1)->count();
        $data['posts_draft_count'] = $data['posts_count'] - $data['posts_active_count'];
        $data['latest_posts'] = $data['user']->posts->where('active', 1)->sortByDesc('updated_at');
        $data['latest_comments'] = $data['user']->comments->sortByDesc('updated_at');
        return view('profile.profile', $data);
    }
}
