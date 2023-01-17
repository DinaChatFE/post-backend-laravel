<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\CredentialsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostInteractionController;
use App\Http\Controllers\UserController;
use App\Models\PostInteraction;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('auth')->group(function () {
    Route::get('profile', [CredentialsController::class, 'profile'])->middleware('auth:api');
    Route::post('authority', [CredentialsController::class, 'authority']);
    Route::post('logout', [CredentialsController::class, 'logout'])->middleware('auth:api');
    Route::post('refresh', [CredentialsController::class, 'refresh']);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [CredentialsController::class, 'register']);
    Route::post('login', [CredentialsController::class, 'login']);
    Route::post('verify_code', [CredentialsController::class, 'code_verification']);
});

Route::middleware('auth:api')->group(function () {
    Route::put('auth/profile', [UserController::class, 'updateProfile']);

    Route::get('posts/my-posts', [PostController::class, 'getAllMyPosts']);
    Route::get('posts/post-following', [PostController::class, 'getFollowingPost']);
    Route::get('posts/post-explore', [PostController::class, 'getExplorePost']);
    Route::post('post/like/{post}', [PostInteractionController::class, 'like']);
    Route::post('post/share/{post}', [PostInteractionController::class, 'share']);

    Route::apiResource('posts', PostController::class);
    Route::apiResource('posts/comment', CommentController::class);

    Route::post('user/follow/{user}', [UserController::class, 'follow']);
    Route::get('user/follower', [UserController::class, 'getFollower']);
    Route::get('user/following', [UserController::class, 'getFollowing']);

});
