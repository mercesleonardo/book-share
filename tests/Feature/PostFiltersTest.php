<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\{Category, Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostFiltersTest extends TestCase
{
    use RefreshDatabase;

    private User $viewer;

    protected function setUp(): void
    {
        parent::setUp();
        // Usuário autenticado necessário para passar pelo authorizeResource.
        $this->viewer = User::factory()->create(['role' => UserRole::USER]);
        $this->actingAs($this->viewer);
    }

    public function test_filter_by_category(): void
    {
        $catA = Category::factory()->create(['name' => 'Tech']);
        $catB = Category::factory()->create(['name' => 'News']);
        Post::factory()->count(2)->create(['category_id' => $catA->id]);
        Post::factory()->count(3)->create(['category_id' => $catB->id]);

        $response = $this->get(route('posts.index', ['category' => $catA->id]));
        $response->assertOk();
        // Garante que somente categoria A aparece nos resultados (não confia no <select> que lista todas as categorias)
        $response->assertViewHas('posts', function ($p) use ($catA, $catB) {
            /** @var \Illuminate\Pagination\LengthAwarePaginator $p */
            $categoryIds = $p->pluck('category_id')->unique()->values()->all();
            return $p->count() === 2 && $categoryIds === [$catA->id] && !in_array($catB->id, $categoryIds, true);
        });
    }

    public function test_filter_by_author(): void
    {
        $authorA = User::factory()->create(['role' => UserRole::USER, 'name' => 'Alice Author']);
        $authorB = User::factory()->create(['role' => UserRole::USER, 'name' => 'Bob Writer']);
        Post::factory()->count(2)->create(['user_id' => $authorA->id]);
        Post::factory()->count(2)->create(['user_id' => $authorB->id]);

        $response = $this->get(route('posts.index', ['author' => $authorA->id]));
        $response->assertOk();
        $response->assertViewHas('posts', function ($p) use ($authorA, $authorB) {
            /** @var \Illuminate\Pagination\LengthAwarePaginator $p */
            $authorIds = $p->pluck('user_id')->unique()->values()->all();
            return $p->count() === 2 && $authorIds === [$authorA->id] && !in_array($authorB->id, $authorIds, true);
        });
    }

    public function test_filter_by_query_in_title(): void
    {
        $cat = Category::factory()->create();
        Post::factory()->create(['title' => 'Laravel Tips', 'category_id' => $cat->id]);
        Post::factory()->create(['title' => 'Symfony Tricks', 'category_id' => $cat->id]);

        $response = $this->get(route('posts.index', ['q' => 'laravel']));
        $response->assertOk();
        $response->assertSee('Laravel Tips');
        $response->assertDontSee('Symfony Tricks');
    }

    public function test_filter_by_query_in_author(): void
    {
        $cat = Category::factory()->create();
        Post::factory()->create(['title' => 'First Title', 'author' => 'Maria Clara', 'category_id' => $cat->id]);
        Post::factory()->create(['title' => 'Second Title', 'author' => 'Joao Silva', 'category_id' => $cat->id]);

        $response = $this->get(route('posts.index', ['q' => 'maria']));
        $response->assertOk();
        $response->assertViewHas('posts', function ($p) {
            /** @var \Illuminate\Pagination\LengthAwarePaginator $p */
            return $p->count() === 1 && $p->first()->author === 'Maria Clara';
        });
    }

    public function test_filter_by_combination(): void
    {
        $catIncluded = Category::factory()->create(['name' => 'IncludedCat']);
        $catExcluded = Category::factory()->create(['name' => 'ExcludedCat']);
        $authorIncluded = User::factory()->create(['role' => UserRole::USER, 'name' => 'Target Author']);
        $authorOther    = User::factory()->create(['role' => UserRole::USER, 'name' => 'Other Author']);

        // Matching post (token no título e autor correspondente)
        Post::factory()->create([
            'title'       => 'Special Match Post',
            'author'      => 'Token Author',
            'category_id' => $catIncluded->id,
            'user_id'     => $authorIncluded->id,
        ]);
        // Noise posts
        Post::factory()->create(['title' => 'Wrong Cat', 'author' => 'Token Author', 'category_id' => $catExcluded->id, 'user_id' => $authorIncluded->id]);
        Post::factory()->create(['title' => 'Wrong Author', 'author' => 'Another Person', 'category_id' => $catIncluded->id, 'user_id' => $authorOther->id]);
        Post::factory()->create(['title' => 'Wrong Query', 'author' => 'Irrelevant', 'category_id' => $catIncluded->id, 'user_id' => $authorIncluded->id]);

        $response = $this->get(route('posts.index', [
            'category' => $catIncluded->id,
            'author'   => $authorIncluded->id,
            'q'        => 'special',
        ]));
        $response->assertOk();
        $response->assertSee('Special Match Post');
        $response->assertDontSee('Wrong Cat');
        $response->assertDontSee('Wrong Author');
        $response->assertDontSee('Wrong Query');
    }

    public function test_filter_no_results(): void
    {
        Category::factory()->create();
        Post::factory()->count(3)->create();

        $response = $this->get(route('posts.index', ['q' => 'stringthatdoesnotexist']));
        $response->assertOk();
        $response->assertSee(__('posts.messages.not_found'));
    }

    public function test_pagination_preserves_filters(): void
    {
        $author = User::factory()->create(['role' => UserRole::USER, 'name' => 'Pag Author']);
        // 20 posts for author to ensure pagination (15 per page)
        Post::factory()->count(20)->create(['user_id' => $author->id]);

        $response = $this->get(route('posts.index', ['author' => $author->id]));
        $response->assertOk();
        // Verifica que a paginação preserva o parâmetro (ordem ou encoding podem variar)
        $response->assertViewHas('posts', function ($p) {
            /** @var \Illuminate\Pagination\LengthAwarePaginator $p */
            return $p->total() === 20 && $p->perPage() === 15 && $p->currentPage() === 1;
        });
        $response->assertSee('author=' . $author->id);
        $response->assertSee('page=2');
    }
}
