<?php

namespace Tests\Feature;

use App\Enums\ModerationStatus;
use App\Models\{Category, Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_approved_posts(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Category $category */
        $category = Category::factory()->create();

        // Criar posts aprovados
        $approvedPosts = Post::factory()->count(3)->create([
            'user_id'           => $user->id,
            'category_id'       => $category->id,
            'moderation_status' => ModerationStatus::Approved,
        ]);

        // Criar posts pendentes (não devem aparecer)
        Post::factory()->count(2)->create([
            'user_id'           => $user->id,
            'category_id'       => $category->id,
            'moderation_status' => ModerationStatus::Pending,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);

        // Verificar se os posts aprovados aparecem
        foreach ($approvedPosts as $post) {
            $response->assertSee($post->title);
        }
    }

    public function test_home_page_shows_empty_state_when_no_approved_posts(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Nenhum livro compartilhado ainda');
        $response->assertSee('Seja o primeiro a compartilhar');
    }

    public function test_home_page_navigation_for_guest_users(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Entrar');
        $response->assertSee('Registrar');
    }

    public function test_home_page_navigation_for_authenticated_users(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Ir para Dashboard');
    }
}
