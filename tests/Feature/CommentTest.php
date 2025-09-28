<?php

namespace Tests\Feature;

use App\Models\{Comment, Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_comment(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)->post('/comments', [
            'post_id' => $post->id,
            'user_id' => $user->id, // will be overridden but fine for validation
            'content' => 'Nice book!'
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => 'Nice book!'
        ]);
    }

    public function test_validation_fails_when_content_missing(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)->post('/comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => ''
        ]);

        $response->assertSessionHasErrors(['content']);
    }

    public function test_user_can_delete_own_comment(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->for(Post::factory())->for($user)->create();

        $response = $this->actingAs($user)->delete('/comments/' . $comment->id);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('comments', [ 'id' => $comment->id ]);
    }
}
