<?php

namespace Tests\Feature;

use App\Enums\ModerationStatus;
use App\Models\{Category, Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostShowEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_displays_navigation_and_related(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create();

        // Criar posts aprovados na mesma categoria para gerar anterior/próximo e relacionados.
        $posts = Post::factory()->count(4)->create([
            'user_id'           => $user->id,
            'category_id'       => $category->id,
            'moderation_status' => ModerationStatus::Approved->value,
        ])->sortBy('id')->values();

        $middle = $posts[1];

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get(route('posts.show', $middle));
        $response->assertStatus(200);
        $response->assertSee(trans('posts.navigation.previous'));
        $response->assertSee(trans('posts.navigation.next'));
        $response->assertSee(trans('posts.related.title'));
    }
}
