<?php

namespace Tests\Feature;

use App\Enums\ModerationStatus;
use App\Models\{Category, Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostPublicShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_post_can_be_viewed_by_public(): void
    {
        // Criar dados necessários
        $user     = User::factory()->create(['name' => 'Test User']);
        $category = Category::factory()->create(['name' => 'Ficção']);

        $post = Post::factory()->create([
            'user_id'           => $user->id,
            'category_id'       => $category->id,
            'title'             => 'Livro de Teste',
            'book_author'       => 'Autor Teste',
            'description'       => 'Descrição do livro de teste',
            'moderation_status' => ModerationStatus::Approved,
            'slug'              => 'livro-de-teste',
        ]);

        // Acessar a página show do post
        $response = $this->get(route('posts.show', $post));

        $response->assertStatus(200);
        $response->assertSee($post->title);
        $response->assertSee($post->book_author);
        $response->assertSee($post->description);
        $response->assertSee($category->name);
        // Note: User name is not displayed in the post-info component,
        // it only shows book author, category, dates, and status
    }

    public function test_pending_post_cannot_be_viewed_by_public(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id'           => $user->id,
            'category_id'       => $category->id,
            'moderation_status' => ModerationStatus::Pending,
            'slug'              => 'post-pendente',
        ]);

        $response = $this->get(route('posts.show', $post));

        $response->assertStatus(404);
    }

    public function test_rejected_post_cannot_be_viewed_by_public(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id'           => $user->id,
            'category_id'       => $category->id,
            'moderation_status' => ModerationStatus::Rejected,
            'slug'              => 'post-rejeitado',
        ]);

        $response = $this->get(route('posts.show', $post));

        $response->assertStatus(404);
    }

    public function test_post_shows_related_posts_from_same_category(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create();

        // Post principal
        $mainPost = Post::factory()->create([
            'user_id'           => $user->id,
            'category_id'       => $category->id,
            'title'             => 'Post Principal',
            'moderation_status' => ModerationStatus::Approved,
        ]);

        // Posts relacionados da mesma categoria
        $relatedPost1 = Post::factory()->create([
            'user_id'           => $user->id,
            'category_id'       => $category->id,
            'title'             => 'Post Relacionado 1',
            'moderation_status' => ModerationStatus::Approved,
        ]);

        $relatedPost2 = Post::factory()->create([
            'user_id'           => $user->id,
            'category_id'       => $category->id,
            'title'             => 'Post Relacionado 2',
            'moderation_status' => ModerationStatus::Approved,
        ]);

        // Post de categoria diferente (não deve aparecer)
        $differentCategory = Category::factory()->create();
        Post::factory()->create([
            'user_id'           => $user->id,
            'category_id'       => $differentCategory->id,
            'title'             => 'Post Categoria Diferente',
            'moderation_status' => ModerationStatus::Approved,
        ]);

        $response = $this->get(route('posts.show', $mainPost));

        $response->assertStatus(200);
        $response->assertSee($relatedPost1->title);
        $response->assertSee($relatedPost2->title);
        $response->assertDontSee('Post Categoria Diferente');
    }

    public function test_post_shows_navigation_links_to_previous_and_next_posts(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create();

        // Criar posts em sequência
        $previousPost = Post::factory()->create([
            'user_id'           => $user->id,
            'category_id'       => $category->id,
            'title'             => 'Post Anterior',
            'moderation_status' => ModerationStatus::Approved,
        ]);

        $currentPost = Post::factory()->create([
            'user_id'           => $user->id,
            'category_id'       => $category->id,
            'title'             => 'Post Atual',
            'moderation_status' => ModerationStatus::Approved,
        ]);

        $nextPost = Post::factory()->create([
            'user_id'           => $user->id,
            'category_id'       => $category->id,
            'title'             => 'Próximo Post',
            'moderation_status' => ModerationStatus::Approved,
        ]);

        $response = $this->get(route('posts.show', $currentPost));

        $response->assertStatus(200);
        $response->assertSee('Anterior');
        $response->assertSee('Próximo');
    }

    public function test_post_displays_user_rating_when_available(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id'           => $user->id,
            'category_id'       => $category->id,
            'moderation_status' => ModerationStatus::Approved,
            'user_rating'       => 4,
        ]);

        $response = $this->get(route('posts.show', $post));

        $response->assertStatus(200);
        $response->assertSee('Avaliação do usuário');
    }

    public function test_home_book_cards_link_to_post_show_page(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id'           => $user->id,
            'category_id'       => $category->id,
            'title'             => 'Livro Testável',
            'moderation_status' => ModerationStatus::Approved,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee(route('posts.show', $post));
    }
}
