<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\{Comment, Post, User};
use App\Policies\CommentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected CommentPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new CommentPolicy();
    }

    public function test_author_of_comment_can_delete(): void
    {
        $user    = User::factory()->create();
        $post    = Post::factory()->for($user)->create();
        $comment = Comment::factory()->for($post)->for($user)->create();

        $this->assertTrue($this->policy->delete($user, $comment));
    }

    public function test_post_author_cannot_delete_foreign_comment_anymore(): void
    {
        $author  = User::factory()->create();
        $other   = User::factory()->create();
        $post    = Post::factory()->for($author)->create();
        $comment = Comment::factory()->for($post)->for($other)->create();

        $this->assertFalse($this->policy->delete($author, $comment));
    }

    public function test_admin_can_delete_any_comment(): void
    {
        $admin   = User::factory()->create(['role' => UserRole::ADMIN]);
        $author  = User::factory()->create();
        $post    = Post::factory()->for($author)->create();
        $comment = Comment::factory()->for($post)->for($author)->create();

        $this->assertTrue($this->policy->delete($admin, $comment));
    }

    public function test_moderator_can_delete_any_comment(): void
    {
        $moderator = User::factory()->create(['role' => UserRole::MODERATOR]);
        $author    = User::factory()->create();
        $post      = Post::factory()->for($author)->create();
        $comment   = Comment::factory()->for($post)->for($author)->create();

        $this->assertTrue($this->policy->delete($moderator, $comment));
    }

    public function test_unrelated_regular_user_cannot_delete(): void
    {
        $author    = User::factory()->create();
        $other     = User::factory()->create();
        $unrelated = User::factory()->create();
        $post      = Post::factory()->for($author)->create();
        $comment   = Comment::factory()->for($post)->for($other)->create();

        $this->assertFalse($this->policy->delete($unrelated, $comment));
    }
}
