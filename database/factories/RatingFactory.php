<?php

namespace Database\Factories;

use App\Models\{Post, Rating, User};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rating>
 */
class RatingFactory extends Factory
{
    protected $model = Rating::class;

    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'user_id' => User::factory(),
            'stars'   => $this->faker->numberBetween(1, 5),
        ];
    }
}
