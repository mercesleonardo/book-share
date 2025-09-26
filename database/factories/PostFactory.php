<?php

namespace Database\Factories;

use App\Models\{Category, Post, User};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'category_id' => Category::factory(),
            'title'       => $this->faker->sentence(6),
            'description' => $this->faker->paragraphs(3, true),
            // Campo de upload real; em testes de factory padrão deixamos null
            'image' => null,
        ];
    }
}
