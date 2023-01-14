<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostInteraction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $postId = $request->query('post_id');
        if ($postId) {
            $treeComment = Comment::where(['post_id' =>  $postId])
                ->orderBy('created_at')->get()->toTree();
        }
        return CommentResource::collection($treeComment ?? []);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCommentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCommentRequest $request)
    {
        $comment = Comment::create($request->validated());
        // insert to POST Interactions
        PostInteraction::create([
            'type' => 'comment',
            'post_id' => $request->post_id,
            'user_id' => auth()->id()
        ]);

        if (!$comment) {
            return Response::json(['message' => 'Your comment didn\'t appeared to have'], 500);
        }
        return Response::json(['message' => 'You been comment on post : "' . $request->text . '"']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCommentRequest  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $comment = $comment->update($request->validated());

        return Response::json(['message' => 'Comment on post update to : "' . $request->text . '"']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $delete  = Comment::where(['id' => $comment->id])->delete();

        return Response::json(['message' => $delete ?  'comment was deleted' : "fail to delete"], $delete ? 200 : 500);
    }
}
