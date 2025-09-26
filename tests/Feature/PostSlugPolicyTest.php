<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\{Category, Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostSlugPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_unique_slug_on_create(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin);

        /** @var Category $category */
        $category = Category::create(['name' => 'Tech']);

        $title = 'My Post Title';
        Post::create(['title' => $title, 'description' => 'Desc', 'category_id' => $category->id, 'user_id' => $admin->id]);
        Post::create(['title' => $title, 'description' => 'Desc 2', 'category_id' => $category->id, 'user_id' => $admin->id]);

        $slugs = Post::pluck('slug')->toArray();
        $this->assertContains('my-post-title', $slugs);
        $this->assertContains('my-post-title-1', $slugs);
    }

    public function test_slug_updates_when_title_changes(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin);
        /** @var Category $category */
        $category = Category::create(['name' => 'News']);

        /** @var Post $post */
        $post = Post::create(['title' => 'Old Title', 'description' => 'x', 'category_id' => $category->id, 'user_id' => $admin->id]);
        $this->assertEquals('old-title', $post->slug);

        $post->update(['title' => 'New Title']);
        $this->assertEquals('new-title', $post->slug);
    }

    public function test_any_authenticated_user_can_create_post(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::USER]);
        $this->actingAs($user);
        /** @var Category $category */
        $category = Category::create(['name' => 'General']);

        $response = $this->post(route('posts.store'), [
            'title'       => 'User Post',
            'description' => 'Body',
            'category_id' => $category->id,
        ]);
        $response->assertRedirect(route('posts.index'));
        $this->assertDatabaseHas('posts', ['title' => 'User Post', 'user_id' => $user->id]);
    }

    public function test_owner_can_update_and_delete_post(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create(['role' => UserRole::USER]);
        $this->actingAs($owner);
        /** @var Category $category */
        $category = Category::create(['name' => 'OwnerCat']);
        /** @var Post $post */
        $post = Post::create(['title' => 'Original', 'description' => 'd', 'category_id' => $category->id, 'user_id' => $owner->id]);

        $update = $this->patch(route('posts.update', $post), [
            'title'       => 'Changed',
            'description' => 'd2',
            'category_id' => $category->id,
        ]);
        $update->assertRedirect(route('posts.index'));
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Changed']);

        // Após atualização o slug muda; recarrega o modelo para obter o novo slug.
        $post->refresh();
        $delete = $this->delete(route('posts.destroy', $post));
        $delete->assertRedirect(route('posts.index'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_moderator_can_update_and_delete_post_of_other_user(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create(['role' => UserRole::USER]);
        /** @var User $moderator */
        $moderator = User::factory()->create(['role' => UserRole::MODERATOR]);
        /** @var Category $category */
        $category = Category::create(['name' => 'Moderation']);
        /** @var Post $post */
        $post = Post::create(['title' => 'Moderated', 'description' => 'd', 'category_id' => $category->id, 'user_id' => $owner->id]);

        $this->actingAs($moderator);
        $update = $this->patch(route('posts.update', $post), [
            'title'       => 'Moderated Updated',
            'description' => 'd2',
            'category_id' => $category->id,
        ]);
        $update->assertRedirect(route('posts.index'));
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Moderated Updated']);

        $post->refresh();
        $delete = $this->delete(route('posts.destroy', $post));
        $delete->assertRedirect(route('posts.index'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_non_owner_cannot_update_or_delete_post(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create(['role' => UserRole::USER]);
        /** @var User $other */
        $other = User::factory()->create(['role' => UserRole::USER]);
        /** @var Category $category */
        $category = Category::create(['name' => 'Others']);
        /** @var Post $post */
        $post = Post::create(['title' => 'Foreign', 'description' => 'd', 'category_id' => $category->id, 'user_id' => $owner->id]);

        $this->actingAs($other);
        $update = $this->patch(route('posts.update', $post), [
            'title'       => 'Attempt',
            'description' => 'd2',
            'category_id' => $category->id,
        ]);
        $update->assertForbidden();

        $delete = $this->delete(route('posts.destroy', $post));
        $delete->assertForbidden();
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }
}
