<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\{Comment, Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentDeletionVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_delete_button_for_foreign_comment(): void
    {
        /** @var User $admin */
        $admin   = User::factory()->create(['role' => UserRole::ADMIN]);
        $author  = User::factory()->create();
        $post    = Post::factory()->for($author)->create();
        $comment = Comment::factory()->for($post)->for($author)->create();

        $response = $this->actingAs($admin)->get(route('posts.show', $post));
        $response->assertStatus(200);
        $response->assertSee(__('comments.form.delete'));
    }

    public function test_moderator_sees_delete_button_for_foreign_comment(): void
    {
        /** @var User $moderator */
        $moderator = User::factory()->create(['role' => UserRole::MODERATOR]);
        $author    = User::factory()->create();
        $post      = Post::factory()->for($author)->create();
        $comment   = Comment::factory()->for($post)->for($author)->create();

        $response = $this->actingAs($moderator)->get(route('posts.show', $post));
        $response->assertStatus(200);
        $response->assertSee(__('comments.form.delete'));
    }

    public function test_regular_unrelated_user_does_not_see_delete_button(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $other */
        $other = User::factory()->create();
        /** @var User $viewer */
        $viewer  = User::factory()->create();
        $post    = Post::factory()->for($owner)->create();
        $comment = Comment::factory()->for($post)->for($other)->create();

        $response = $this->actingAs($viewer)->get(route('posts.show', $post));
        $response->assertStatus(200);
        $response->assertDontSee(__('comments.form.delete'));
    }

    public function test_post_owner_no_longer_sees_delete_button_for_foreign_comment(): void
    {
        /** @var User $owner */
        $owner   = User::factory()->create();
        $other   = User::factory()->create();
        $post    = Post::factory()->for($owner)->create();
        $comment = Comment::factory()->for($post)->for($other)->create();

        $response = $this->actingAs($owner)->get(route('posts.show', $post));
        $response->assertStatus(200);
        $response->assertDontSee(__('comments.form.delete'));
    }
}
