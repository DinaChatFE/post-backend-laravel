<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function getFollower()
    {
        $user = auth()->user();
        return UserResource::collection($user->follower);
    }

    public function getFollowing()
    {
        $user = auth()->user();
        return UserResource::collection($user->following);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = User::find(auth()->id());
        $request->collect()->each(function($v, $k) use($user)  {
            $user->$k = $v;
        });
        $user->save();

        return new UserResource($user);
    }

    public function follow(User $user)
    {
        //@TODO add connections suggestion, query from friend who following you

        /**
         * @var User
         */
        $u = auth()->user();

        if ($u->id === $user->id) {
            return response()->json(['message' => 'You cannot follow your self']);
        }

        if ($isFollow = $u->following()->where(['following_id' => $user->id])->first()) {
            $u->following()->detach($user->id);
        } else {
            $u->following()->attach($user->id, ['created_at' => now() ]);
        }

        return response()->json(['message' => 'Successfully ' . ($isFollow ? 'un-following' : 'follow')]);
    }
}
