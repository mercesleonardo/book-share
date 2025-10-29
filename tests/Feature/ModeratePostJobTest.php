<?php

namespace Tests\Feature;

use App\Enums\ModerationStatus;
use App\Models\{Category, Post, User};
use App\Notifications\PostModerationStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Http, Notification};
use Tests\TestCase;

class ModeratePostJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_is_approved_when_openai_returns_not_flagged(): void
    {
        Notification::fake();

        Http::fake([
            '*' => Http::response(['results' => [['flagged' => false]]], 200),
        ]);

        // Criar usuário e categoria (normalmente) e o post — o Observer dispara o Job automaticamente
        $user     = User::factory()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
        ]);

        $post->refresh();

        $this->assertSame(ModerationStatus::Approved->value, $post->moderation_status->value ?? $post->moderation_status);

        Notification::assertSentTo($post->user, PostModerationStatusChanged::class);
    }

    public function test_post_is_rejected_when_openai_returns_flagged(): void
    {
        Notification::fake();

        Http::fake([
            '*' => Http::response(['results' => [['flagged' => true]]], 200),
        ]);

        $user     = User::factory()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
        ]);

        $post->refresh();

        $this->assertSame(ModerationStatus::Rejected->value, $post->moderation_status->value ?? $post->moderation_status);

        Notification::assertSentTo($post->user, PostModerationStatusChanged::class);
    }
}
