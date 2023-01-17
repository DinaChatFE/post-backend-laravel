<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostInteraction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostInteractionFactory extends Factory
{
    protected $model = PostInteraction::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::get()->random()->id,
            'post_id' => Post::get()->random()->id,
            'type' => ['like', 'comment', 'share'][random_int(0, 2)],
        ];
    }
}
