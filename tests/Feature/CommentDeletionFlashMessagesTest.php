<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\{Comment, Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentDeletionFlashMessagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Autor do comentário remove seu próprio comentário.
     */
    public function test_author_gets_removed_self_flash_message(): void
    {
        $user    = User::factory()->create();
        $post    = Post::factory()->for($user)->create();
        $comment = Comment::factory()->for($post)->for($user)->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->delete(route('comments.destroy', $comment));

        $response->assertRedirect();
        $response->assertSessionHas('status', __('comments.removed_self'));
    }

    /**
     * Moderador remove comentário de outro usuário não sendo dono nem autor.
     */
    public function test_moderator_gets_removed_as_moderator_message(): void
    {
        $owner     = User::factory()->create();
        $commenter = User::factory()->create();
        $moderator = User::factory()->create(['role' => UserRole::MODERATOR]);
        $post      = Post::factory()->for($owner)->create();
        $comment   = Comment::factory()->for($post)->for($commenter)->create();

        /** @var \App\Models\User $moderator */
        $response = $this->actingAs($moderator)->delete(route('comments.destroy', $comment));

        $response->assertRedirect();
        $response->assertSessionHas('status', __('comments.removed_as_moderator'));
    }

    /**
     * Admin remove comentário de outro usuário não sendo dono nem autor.
     */
    public function test_admin_gets_removed_as_moderator_message(): void
    {
        $owner   = User::factory()->create();
        $user    = User::factory()->create();
        $admin   = User::factory()->create(['role' => UserRole::ADMIN]);
        $post    = Post::factory()->for($owner)->create();
        $comment = Comment::factory()->for($post)->for($user)->create();

        /** @var \App\Models\User $admin */
        $response = $this->actingAs($admin)->delete(route('comments.destroy', $comment));

        // Admin usa mesma chave de moderador (removido por privilégios)
        $response->assertRedirect();
        $response->assertSessionHas('status', __('comments.removed_as_moderator'));
    }
}
