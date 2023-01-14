<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostInteraction;
use Illuminate\Http\Request;

class PostInteractionController extends Controller
{
    public function like(Post $post)
    {
        $map = ['type' => 'like', 'post_id' => $post->id, 'user_id' => auth()->id()];

        if ($isLike = PostInteraction::firstWhere($map)) {
            $isLike->delete();
        } else {
            PostInteraction::create($map);
        }

        return response()->json(['message' => 'Successfully ' . (!$isLike ? 'like' : 'remove like') . ' the post']);
    }

    public function share(Post $post)
    {
        $map = ['type' => 'share', 'post_id' => $post->id, 'user_id' => auth()->id()];

        if ($isShare = PostInteraction::firstWhere($map)) {
            $isShare->delete();
        } else {
            PostInteraction::create($map);
        }

        return response()->json(['message' => 'Successfully ' . ($isShare ? 'share' : 'delete the share') . ' of the post']);
    }
}
