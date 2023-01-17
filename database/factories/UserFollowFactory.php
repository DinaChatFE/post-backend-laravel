<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserFollow;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFollowFactory extends Factory
{
    protected $model = UserFollow::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $userId = User::get()->random()->id;
        $followId = User::where('id', '!=', $userId)->get()->random()->id;
        return [
            'user_id' => $userId,
            'following_id' => $followId,
        ];
    }
}
