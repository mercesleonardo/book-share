<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\{Category, Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostAuthorFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_without_author_uses_user_name(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::USER, 'name' => 'Fallback User']);
        $this->actingAs($user);
        /** @var Category $category */
        $category = Category::create(['name' => 'Gen']);

        $response = $this->post(route('admin.posts.store'), [
            'title'       => 'No Author Title',
            'description' => 'Body',
            'category_id' => $category->id,
            // intentionally no author
        ]);

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertDatabaseHas('posts', [
            'title'       => 'No Author Title',
            'book_author' => 'Fallback User',
            'user_id'     => $user->id,
        ]);
    }

    public function test_update_without_author_keeps_or_sets_fallback(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::USER, 'name' => 'Fallback Updater']);
        $this->actingAs($user);
        /** @var Category $category */
        $category = Category::create(['name' => 'Cat']);
        /** @var Post $post */
        $post = Post::create([
            'title'       => 'Original',
            'book_author' => 'Custom Author',
            'description' => 'd',
            'category_id' => $category->id,
            'user_id'     => $user->id,
            'user_rating' => 5,
        ]);

        $response = $this->patch(route('admin.posts.update', $post), [
            'title'       => 'Changed Title',
            'description' => 'd2',
            'category_id' => $category->id,
            // no author provided
        ]);

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertDatabaseHas('posts', [
            'id'          => $post->id,
            'title'       => 'Changed Title',
            'book_author' => 'Fallback Updater', // updated to fallback (since we replaced with current user when missing)
        ]);
    }
}
