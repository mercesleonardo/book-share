<?php

namespace Tests\Feature;

use App\Enums\ModerationStatus;
use App\Models\{Category, Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_rating_from_public_page_redirects_back_to_public_page(): void
    {
        // Criar usuário e fazer login
        $user = User::factory()->create();
        $this->actingAs($user);

        // Criar category e post aprovado
        $category = Category::factory()->create();
        $post     = Post::factory()->create([
            'category_id'       => $category->id,
            'moderation_status' => ModerationStatus::Approved,
            'slug'              => 'test-book',
        ]);

        // Primeiro visitar a página pública para estabelecer o referer
        $this->get(route('posts.show', $post));

        // Submeter avaliação através da rota pública
        $response = $this->post(route('posts.ratings.store', $post), [
            'stars' => 4,
        ]);

        // Verificar que redirecionou de volta para a página pública
        $response->assertRedirect(route('posts.show', $post));
        $response->assertSessionHas('success');

        // Verificar que a avaliação foi salva
        $this->assertDatabaseHas('ratings', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'stars'   => 4,
        ]);
    }

    public function test_rating_from_admin_page_redirects_back_to_admin_page(): void
    {
        // Criar usuário e fazer login
        $user = User::factory()->create();
        $this->actingAs($user);

        // Criar category e post
        $category = Category::factory()->create();
        $post     = Post::factory()->create([
            'category_id' => $category->id,
        ]);

        // Primeiro visitar a página admin para estabelecer o referer
        $this->get(route('admin.posts.show', $post));

        // Submeter avaliação através da rota admin
        $response = $this->post(route('admin.posts.ratings.store', $post), [
            'stars' => 5,
        ]);

        // Verificar que redirecionou de volta para a página admin
        $response->assertRedirect(route('admin.posts.show', $post));
        $response->assertSessionHas('success');

        // Verificar que a avaliação foi salva
        $this->assertDatabaseHas('ratings', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'stars'   => 5,
        ]);
    }
}
