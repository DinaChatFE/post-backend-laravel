<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $followUser = auth()->user()->following()->pluck('users.id')->toArray();
        $post = Post::with(['user', 'postInteractions'])->where(function ($q) use ($followUser) {
            $q->whereIn('posts.user_id', $followUser)->orWhereHas('postInteractions');
        })
            ->where('user_id', '!=', auth()->id())
            ->inRandomOrder()
            ->paginate(config('const.pagination.length'));

        /*!Todo:
         * Accept when another post has both ref from following and post interactions
         * Checking conditions if the post is from the following user no post interactions
         * Currently is from post interactions
         */

        return PostResource::collection($post);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostRequest $request)
    {
        $post = Post::create(array_merge($request->validated(), ['user_id' => auth()->id()]));

        return new PostResource($post->load('user'));
    }

    public function getFollowingPost()
    {
        $query = Post::with(['user'])->whereHas(
            'user',
            function ($query) {
                $query->whereHas('follower');
            }
        )->where('user_id', '!=', auth()->id());

        $query = $query->inRandomOrder()->paginate(config('const.pagination.length'));

        return PostResource::collection($query);
    }

    public function getExplorePost()
    {
        $query = Post::with(['user'])->where('user_id', '!=', auth()->id());

        $query = $query->inRandomOrder()->paginate(config('const.pagination.length'));

        return PostResource::collection($query);
    }

    /**
     * Get post by authorize users
     *
     * @return Response
     */
    public function getAllMyPosts()
    {
        /**
         * @var User
         */
        $user = auth()->user();
        $posts = $user->posts()->with('user')->latest()->paginate(config('const.pagination.length'));
        return PostResource::collection($posts);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePostRequest  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $request->collect()->each(function ($v, $k) use ($post) {
            $post->$k = $v;
        });
        $post->save();

        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $delete = $post->delete();
        if (!$delete) {
            Response::json(['message' => "Failed to delete the record"]);
        }

        return Response::json(['message' => "Successfully Delete The Record"]);
    }

    public function show(Post $post)
    {
        return new PostResource($post->load('user'));
    }
}
