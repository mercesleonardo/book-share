<?php

namespace Tests\Feature;

use App\Models\{Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingServiceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_rate_and_update_rating(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $author */
        $author = User::factory()->create();
        $post   = Post::factory()->create(['user_id' => $author->id]);

        $this->actingAs($user)
            ->post(route('posts.ratings.store', $post))
            ->assertSessionHasErrors();

        $this->actingAs($user)
            ->post(route('posts.ratings.store', $post), ['stars' => 3])
            ->assertRedirect();

        $this->assertDatabaseHas('ratings', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'stars'   => 3,
        ]);

        // Update
        $this->actingAs($user)
            ->post(route('posts.ratings.store', $post), ['stars' => 5])
            ->assertRedirect();

        $this->assertDatabaseHas('ratings', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'stars'   => 5,
        ]);
    }
}
