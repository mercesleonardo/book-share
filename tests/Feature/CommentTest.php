<?php

namespace Tests\Feature;

use App\Models\{Comment, Post, User};
use App\Notifications\NewCommentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_comment(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user, 'web')->post('/comments', [
            'post_id' => $post->id,
            'user_id' => $user->id, // will be overridden but fine for validation
            'content' => 'Nice book!',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => 'Nice book!',
        ]);
    }

    public function test_validation_fails_when_content_missing(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user, 'web')->post('/comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['content']);
    }

    public function test_user_can_delete_own_comment(): void
    {
        /** @var User $user */
        $user    = User::factory()->create();
        $comment = Comment::factory()->for(Post::factory())->for($user)->create();

        $response = $this->actingAs($user, 'web')->delete('/comments/' . $comment->id);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_user_cannot_delete_foreign_comment(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $other */
        $other   = User::factory()->create();
        $comment = Comment::factory()->for(Post::factory()->for($owner))->for($owner)->create();

        $response = $this->actingAs($other, 'web')->delete('/comments/' . $comment->id);
        $response->assertStatus(403);
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    public function test_post_author_can_delete_foreign_comment_on_own_post(): void
    {
        /** @var User $author */
        $author = User::factory()->create();
        /** @var User $commenter */
        $commenter = User::factory()->create();
        $post      = Post::factory()->for($author)->create();
        $comment   = Comment::factory()->for($post)->for($commenter)->create();

        $response = $this->actingAs($author, 'web')->delete('/comments/' . $comment->id);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_new_comment_triggers_notification_to_post_author(): void
    {
        /** @var User $author */
        $author = User::factory()->create();
        /** @var User $commenter */
        $commenter = User::factory()->create();
        $post      = Post::factory()->for($author)->create();

        Notification::fake();

        $response = $this->actingAs($commenter, 'web')->post('/comments', [
            'post_id' => $post->id,
            'user_id' => $commenter->id,
            'content' => 'Great book.',
        ]);

        $response->assertSessionHasNoErrors();

        Notification::assertSentTo($author, NewCommentNotification::class);
    }

    public function test_comment_appears_on_post_view(): void
    {
        /** @var User $author */
        $author = User::factory()->create();
        /** @var User $commenter */
        $commenter = User::factory()->create();
        $post      = Post::factory()->for($author)->create();
        $comment   = Comment::factory()->for($post)->for($commenter)->create(['content' => 'Visible content']);

        $response = $this->actingAs($author, 'web')->get('/posts/' . $post->slug);
        $response->assertSee('Visible content');
        $response->assertSee($commenter->name);
    }

    public function test_admin_can_delete_any_comment(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => \App\Enums\UserRole::ADMIN]);
        /** @var User $author */
        $author  = User::factory()->create();
        $post    = Post::factory()->for($author)->create();
        $comment = Comment::factory()->for($post)->for($author)->create();

        $response = $this->actingAs($admin, 'web')->delete('/comments/' . $comment->id);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_moderator_can_delete_any_comment(): void
    {
        /** @var User $moderator */
        $moderator = User::factory()->create(['role' => \App\Enums\UserRole::MODERATOR]);
        /** @var User $author */
        $author  = User::factory()->create();
        $post    = Post::factory()->for($author)->create();
        $comment = Comment::factory()->for($post)->for($author)->create();

        $response = $this->actingAs($moderator, 'web')->delete('/comments/' . $comment->id);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
