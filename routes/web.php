<?php

use App\Models\Post;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::bind('posts', function ($value, $route){
    return Post::whereSlug($value)->first();
});

Route::group(['middleware' => ['auth']], function (){

    Route::get('/', 'PostController@index');

    Route::get('/home', 'PostController@index');

    Route::get('/my-all-posts','UserController@getAllUserPosts');

    Route::get('/my-drafts','UserController@getUserDraftPosts');

    Route::post('/posts/update','PostController@updatePost');

    Route::get('/posts/destroy/{id}', 'PostController@destroy');

    Route::resource('posts', 'PostController');

    Route::resource('posts.comments', 'CommentController');
});

Route::get('/user/{id}','UserController@profile')->where('id', '[0-9]+');

Route::get('/user/{id}/posts','UserController@getUserPosts')->where('id', '[0-9]+');
