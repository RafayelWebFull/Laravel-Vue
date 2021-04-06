<?php

namespace App\Http\Controllers;

use App\Category;
use App\Comment;
use App\CommentsReply;
use App\Http\Requests\Blogger\NewPost;
use App\Post;
use App\User    ;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use PhpParser\Node\Expr\Cast\Object_;

class PostController extends Controller
{
    public function index(){
        $posts = Post::query()->with(["comments","category"])->where("status_created","=",null)->where("status","=","active")->inRandomOrder()->paginate(10);
        $comment_reply = DB::table("comments_replies")->get();
        $categories = DB::table("categories")->select("category_name","id")->get()->toArray();
        return response()->json([
            'success' => true,
            'data' => $posts,
            'comments_reply' => $comment_reply,
            "categories" => $categories
        ]);
    }
    public function comments(Request $request){
        $comment = Comment::query()->create([
                "comment_author" => $request->get('author'),
                "comment" => $request->get('comment'),
                "post_id" => $request->get('post_id'),
        ]);
        $posts = Post::query()->with(["comments","category"])->where("id","=",$request->get("post_id"))->get();
            return response()->json([
               "success" => true,
                "data" => $posts,
                "post_index" => $request->get("id")
            ]);
    }
    public function search_filter(Request $request){
        $search = $request->all();
        $search_name = $search[0];
        $posts = Post::query()->where("status_created","=",NULL)->where("status","=","active")->where(function($query) use ($search_name){
            $query->where("title","like", "%". $search_name ."%")->orWhere("author","like", "%". $search_name ."%");
        })->with(["comments","category"])->paginate(10);
//        $posts = Post::query()->where("status_created","=",NULL)->where("title","like", "%". $search[0] ."%")
//            ->orWhere("author","like", "%". $search[0] ."%")
//            ->with(["comments","category"])->paginate(10);
        return response()->json([
           "data" => $posts
        ]);
    }
    public function searchByCategory(Request $request){
        $category = $request->keys();
        $posts = Post::query()->where("category_id","=",$category[0])->get();
       return response()->json([
          "data" => $posts
       ]);
    }
    public function BloggerPosts(Request $request){
        $bloggerName = $request->all();
        $posts = Post::query()->where("author","=",$bloggerName[0]["name"])->with(["comments","category"])->paginate(10);
        $posts = Post::query()->where("author","=",$bloggerName[0]["name"])->with(["comments","category"])->paginate(10);
        return response()->json([
            "data" => $posts,
        ]);

    }
    public function ChangeStatus(Request $request){
       $result = $request->all();
       $posts = Post::query()->where("id","=",$result[1])->first();
       $posts->status =$result[0];
       $posts->update();
    }
    public function fileUpload($data){
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 6; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        if($data){
            $fileToArray = $data["file"];
            $fileName = $_FILES["file"]["name"];
            $fileToArray->storeAs("/public/postsPhoto/",$randomString . $fileName );
            $fileHref = $randomString .  $fileName ;
            return $fileHref;
        }
    }
    public function filterByStatus(Request $request){
        $bloggerName = $request->all();
        if($bloggerName[0] == 'Created'){
        $posts = Post::query()->with(["comments","category"])->where("author","=",$bloggerName[1]["name"])->orderBy("created_time","DESC")->paginate(10);
             return response()->json([
                 "data" => $posts
             ]);
        }else{
            $posts = Post::query()->where("author","=",$bloggerName[1]["name"])
                ->where("status","=",$bloggerName[0])
                ->with(["comments","category"])
                ->paginate(10);
            return response()->json([
                "data" => $posts
            ]);
        }
    }
    public function newPosts(NewPost $request){
        if($request->file()){
            $fileHref =  $this->fileUpload($request->all("file"));
        }else{
            $fileHref = "default.png";
        }
        $request->request->add(["imageName" => $fileHref]);
        $post = Post::query()->create([
                "image" => $request->get("imageName"),
                "title" => $request->get("title"),
                "description" => $request->get("description"),
                "author" => $request->get("author"),
                "category_id" => $request->get("category_id"),
                "status" => $request->get("status"),
                "created_time" => Carbon::now()->format("Y-m-d")
        ]);
        return response()->json([
            "massage" => "admin should see your post",
           "success" =>  true
        ]);
    }
    public function edit(Request $request){
        $post = Post::query()->where("id","=",$request->get("0"))->first();
        return response()->json([
           "data" => $post,
        ]);
    }
    public function update(Request $request){
        if($request->file()){

            $fileHref =  $this->fileUpload($request->all("file"));
        }else{
            $fileHref = "default.png";
        }
        $request->request->add(["imageName" => $fileHref]);
        $post = Post::query()->where("id",'=',$request->get("id"))->first();

        $post->update([
            "image" => $request->get("imageName"),
            "title" => $request->get("title"),
            "description" => $request->get("description"),
            "author" => $request->get("author"),
            "category_id" => $request->get("category_id"),
            "status" => $request->get("status"),
            "created_time" => Carbon::now()->format("Y-m-d")
        ]);
        $posts = Post::query()->where("author","=",$request->get("author"))->paginate(10);
        return response()->json([
            "data" => $posts
        ]);
    }
    public function delete(Request $request){
        $post = Post::query()->where("id",'=',$request->get(0))->delete();
        $posts = $posts = Post::query()->where("author","=",$request->get(1))->paginate(10);
        return response()->json([
            "success" => true,
            "data" => $posts
        ]);
    }
    public function show(Request $request){
        $post = Post::query()->with(["comments","category"])->where("id",'=',$request->get(0))->get();
        $comment_reply = CommentsReply::all();
        return response()->json([
            "data" => $post,
            "comment_reply" => $comment_reply
        ]);
    }
        public function addComment(Request $request){
        $comment = Comment::query()->create([
            "comment_author" => $request->get('author'),
            "comment" => $request->get('comment'),
            "post_id" => $request->get('post_id'),
        ]);
        $post = Post::query()->with(["comments","category"])->where("id",'=',$request->get("post_id"))->get();
        $comment_reply = CommentsReply::all();
        return response()->json([
            "data" => $post,
            "comment_reply" => $comment_reply
        ]);
    }
    public function searchBlogger(Request $request){

        $blogger = User::query()->where("role","=","blogger")->where("name","LIKE","%" . $request->get(0) . "%")->paginate(10);
        return response()->json([
            "data" => $blogger
        ]);
    }
    public function allPosts(){
        $posts = Post::query()->with(["comments","category"])->paginate(10);
        $comment_reply = DB::table("comments_replies")->get();
        $categories = DB::table("categories")->select("category_name","id")->get()->toArray();
        return response()->json([
            'success' => true,
            'data' => $posts,
            'comments_reply' => $comment_reply,
            "categories" => $categories
        ]);
    }
    public function newpostslist(){
        $posts = Post::query()->where("status_created","=","new")->with(["comments","category"])->paginate(10);
        return response()->json([
           "data" => $posts
        ]);
    }
    public function searchNewPosts(Request $request){
        $search = $request->all();
        $posts = Post::query()->where("status_created","=","new")->where("title","like", "%". $search[0] ."%")
            ->orWhere("author","like", "%". $search[0] ."%")
            ->with(["comments","category"])->paginate(10);
        return response()->json([
            "data" => $posts
        ]);
    }
    public function changeCreatedStatus(Request $request){
        $request = $request->all();
        $posts = Post::query()->where("id",'=',$request[1])->first();
        $posts->status_created = $request[0];
        $posts->save();
        return response()->json([
           "data" =>$posts
        ]);
    }
    public function commentReply(Request $request){
        $commentReply = CommentsReply::query()->create([
            "comment_reply_author" => $request->get('comment_reply_author'),
            "comment_reply" => $request->get('comment_reply'),
            "comment_id" => $request->get('comment_id'),
        ]);
        $commentReplys = DB::table("comments_replies")->get();
        return response()->json([
           "data" => $commentReplys
        ]);
    }
    public function commentReplyBlogger(Request $request){
        $commentReply = CommentsReply::query()->create([
            "comment_reply_author" => $request->get('comment_reply_author'),
            "comment_reply" => $request->get('comment_reply'),
            "comment_id" => $request->get('comment_id'),
        ]);
        $post = Post::query()->where("id",'=',$request->get('post_id'))->get();
        return response()->json([
           "data" => $post
        ]);
    }
}
