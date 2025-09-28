<?php

namespace Tests\Feature;

use App\Models\{Category, Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RatingsCacheCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_clear_all_ratings_cache_command(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create();
        $post     = Post::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'user_rating' => 4,
        ]);

        Cache::put('post:ratings:avg:' . $post->id, 3.5, 600);
        Cache::put('post:ratings:count:' . $post->id, 2, 600);

        $this->artisan('ratings:clear')
            ->expectsOutput('Cache de ratings limpo para todos os posts.')
            ->assertExitCode(0);

        $this->assertFalse(Cache::has('post:ratings:avg:' . $post->id));
        $this->assertFalse(Cache::has('post:ratings:count:' . $post->id));
    }

    public function test_clear_single_post_ratings_cache_command(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create();
        $post     = Post::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'user_rating' => 4,
        ]);

        Cache::put('post:ratings:avg:' . $post->id, 4.0, 600);
        Cache::put('post:ratings:count:' . $post->id, 1, 600);

        $this->artisan('ratings:clear ' . $post->id)
            ->expectsOutput("Cache de ratings limpo para o post ID {$post->id}.")
            ->assertExitCode(0);

        $this->assertFalse(Cache::has('post:ratings:avg:' . $post->id));
        $this->assertFalse(Cache::has('post:ratings:count:' . $post->id));
    }
}
