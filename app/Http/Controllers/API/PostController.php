<?php

namespace App\Http\Controllers\API;

use App\Events\PostCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    protected $cacheDuration;

    public function __construct()
    {
        $this->cacheDuration = env('CACHE_DURATION', 60);
    }

    public function index(){
        $posts = Cache::remember('posts:all', $this->cacheDuration, function () {
            return Post::paginate(10);
        });
        return PostResource::collection($posts);
    }

    public function show(Post $post){
        $id = $post->id;
        $post = Cache::remember('posts:'.$id, $this->cacheDuration, function()use($post){
            return $post;
        });
        return new PostResource($post);
    }

    public function store(StorePostRequest $request){
        $post = Post::create($request->validated());
        Cache::forget('posts:all');
        return new PostResource($post);
    }

    public function update(Post $post, UpdatePostRequest $request){
        $post->update($request->validated()); 
        $id = $post->id;
        
        Cache::forget("posts:$id");
        Cache::forget("posts:all");

        return new PostResource($post);
    }

    public function destroy(Post $post, Request $request){
        $post->delete();
        $id = $post->id;
        Cache::forget("posts:$id");
        Cache::forget("posts:all");
        return response()->json([
            'message' => 'Post deleted successfully'
        ], 200); 
    }
}
