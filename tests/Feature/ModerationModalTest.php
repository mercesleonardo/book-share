<?php

namespace Tests\Feature;

use App\Models\{Post, User};
// Assuming Post model exists
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModerationModalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a moderator/admin user
        $this->user = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function dashboard_contains_single_dynamic_moderation_modal_and_translations(): void
    {
        // Arrange: create a pending post owned by another user to appear in moderation queue
        $author = User::factory()->create();

        // If Post factory not existing, we can skip actual creation - but assuming it exists
        if (class_exists(Post::class)) {
            Post::factory()->create([
                'user_id'           => $author->id,
                'moderation_status' => 'pending',
            ]);
        }

        // Act
        $response = $this->actingAs($this->user)->get('/dashboard');

        // Assert core modal markup & translation keys
        $response->assertStatus(200);
        $response->assertSee('moderate-post'); // modal name attribute occurrence
        $response->assertSee(trans('dashboard.actions.cancel'));
        $response->assertSee(trans('dashboard.moderation.approve'));
        $response->assertSee(trans('dashboard.moderation.reject'));

        // Ensure we don't have duplicated approve modal names like approve-post-1 pattern
        $response->assertDontSee('approve-post-');
        $response->assertDontSee('reject-post-');
    }
}
