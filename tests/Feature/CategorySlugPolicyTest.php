<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\{Category, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorySlugPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_unique_slug_on_create(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin);

        // Primeiro cria uma categoria inicial
        Category::create(['name' => 'My Sample Category']);
        // Agora cria outra com mesmo nome simulando colisão de slug mas nome diferente (regra unique name impede);
        // Para contornar a unique constraint de "name" e ainda testar colisão de slug, criamos nomes que produzem o mesmo slug.
        // Ex: "My   Sample   Category" e "My Sample  Category" ambos viram "my-sample-category" após Str::slug.
        Category::create(['name' => 'My   Sample   Category']);
        Category::create(['name' => 'My Sample  Category']);

        $slugs = Category::orderBy('id')->pluck('slug')->toArray();
        $this->assertEquals('my-sample-category', $slugs[0]);
        $this->assertEquals('my-sample-category-1', $slugs[1]);
        $this->assertEquals('my-sample-category-2', $slugs[2]);
    }

    public function test_updates_slug_when_name_changes(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin);

        $category = Category::create(['name' => 'Original Name']);
        $this->assertEquals('original-name', $category->slug);

        $category->update(['name' => 'New Name']);
        $this->assertEquals('new-name', $category->slug);
    }

    public function test_non_admin_cannot_create_category(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::USER]);
        $this->actingAs($user);

        $response = $this->post(route('categories.store'), ['name' => 'Test']);
        $response->assertForbidden();
    }

    public function test_admin_can_create_category(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin);

        $response = $this->post(route('categories.store'), ['name' => 'Allowed']);
        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Allowed']);
    }
}
