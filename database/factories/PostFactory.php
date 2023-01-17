<?php

namespace Database\Factories;

use App\Traits\FactoryExploreFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    use FactoryExploreFile;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->paragraph(1),
            'description' => $this->faker->paragraph(random_int(1, 4)),
            'images' => $this->randomMultiplePath('/storage/faker/posts/*'),
        ];
    }
}
