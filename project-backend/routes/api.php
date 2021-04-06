<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::resource('posts', 'PostController');
Route::match(["POST","GET"],"comment","PostController@comments");
Route::post("/search","PostController@search_filter");
Route::post("/searchByCategory","PostController@searchByCategory");
Route::post("/searchBlogger","PostController@searchBlogger");
Route::post("/bloggerPosts","PostController@BloggerPosts");
Route::post("/changeStatus","PostController@ChangeStatus");
Route::post("/filterByStatus","PostController@filterByStatus");
Route::post("/newPosts","PostController@newPosts");
Route::post("/editPost","PostController@edit");
Route::post("/updatePost","PostController@update");
Route::post("/deletePost","PostController@delete");
Route::post("/showPost","PostController@show");
Route::post("/addComment","PostController@addComment");
Route::post("/commentReply","PostController@commentReply");
Route::post("/commentReplyBlogger","PostController@commentReplyBlogger");
Route::post("/newPostsList","PostController@newpostslist");
Route::post("/searchNewPosts","PostController@searchNewPosts");
Route::post("/allPosts","PostController@allPosts");
Route::post("/changeCratedStatus","PostController@changeCreatedStatus");
//Route::post("/api/register","UserController@register");




Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::match(["get","post"],'register', 'UserController@register');
    Route::post('verify', 'UserController@verify');
    Route::post('updateUser', 'UserController@updateUser');
    Route::post('updateUserPassword', 'UserController@updateUserPassword');
    Route::match(["GET","POST"],"avatar-upload","UserController@updateUser");
    Route::post("changePassword","UserController@changePassword");
    Route::get("bloggerList","UserController@bloggerList");
    Route::post("blockUser","UserController@blockUser");
    Route::post("checkTime","UserController@checkTime");
});
