<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $comment = Comment::get();
        if ($comment->count()) {
            $comment = $comment->random()->id;
        } else {
            $comment = null;
        }
        return [
            'text' => $this->faker->paragraph(random_int(1, 2)),
            'user_id' => User::get()->random()->id,
            'post_id' => Post::get()->random()->id,
            'parent_id' => random_int(0, 10) > 4 ? null : $comment
        ];
    }
}
