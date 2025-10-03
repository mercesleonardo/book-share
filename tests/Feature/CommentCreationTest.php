<?php

namespace Tests\Feature;

use App\Models\{Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_comment_without_user_id_field(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->post(route('admin.comments.store'), [
            'post_id' => $post->id,
            'content' => 'Meu comentário simples.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => 'Meu comentário simples.',
        ]);
    }

    public function test_sending_user_id_is_rejected(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->post(route('admin.comments.store'), [
            'post_id' => $post->id,
            'user_id' => 9999,
            'content' => 'Outro comentário.',
        ]);

        $response->assertSessionHasErrors(['user_id']);
        $this->assertDatabaseMissing('comments', [
            'post_id' => $post->id,
            'content' => 'Outro comentário.',
        ]);
    }
}
