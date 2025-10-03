<?php

namespace Tests\Feature;

use App\Enums\ModerationStatus;
use App\Models\{Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');
    }

    protected function createModerator(): User
    {
        return User::factory()->create(['role' => 'admin']); // assume admin tem permissão
    }

    public function test_pending_post_appears_in_queue(): void
    {
        $moderator = $this->createModerator();
        $post      = Post::factory()->create(); // default pending

        $this->actingAs($moderator)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee($post->title);
    }

    public function test_approve_moves_post_out_of_queue(): void
    {
        $moderator = $this->createModerator();
        $post      = Post::factory()->create();

        $this->actingAs($moderator)
            ->patch(route('admin.posts.approve', $post))
            ->assertRedirect();

        $this->assertEquals(ModerationStatus::Approved, $post->fresh()->moderation_status);

        $this->actingAs($moderator)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee($post->title);
    }

    public function test_reject_moves_post_out_of_queue(): void
    {
        $moderator = $this->createModerator();
        $post      = Post::factory()->create();

        $this->actingAs($moderator)
            ->patch(route('admin.posts.reject', $post))
            ->assertRedirect();

        $this->assertEquals(ModerationStatus::Rejected, $post->fresh()->moderation_status);

        $this->actingAs($moderator)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee($post->title);
    }

    public function test_regular_user_cannot_approve(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'user']);
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->patch(route('admin.posts.approve', $post))
            ->assertForbidden();
    }
}
