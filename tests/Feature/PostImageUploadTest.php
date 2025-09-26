<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\{Category, Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_post_with_image(): void
    {
        Storage::fake('public');
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::USER]);
        $this->actingAs($user);
        /** @var Category $category */
        $category = Category::create(['name' => 'Images']);

        $file     = UploadedFile::fake()->image('photo.jpg', 640, 480);
        $response = $this->post(route('posts.store'), [
            'title'       => 'With Image',
            'description' => 'Body',
            'category_id' => $category->id,
            'image'       => $file,
        ]);

        $response->assertRedirect(route('posts.index'));
        $post = Post::first();
        $this->assertNotNull($post->image);
        $this->assertTrue(Storage::disk('public')->exists($post->image));
    }

    public function test_store_post_without_image(): void
    {
        Storage::fake('public');
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::USER]);
        $this->actingAs($user);
        /** @var Category $category */
        $category = Category::create(['name' => 'NoImage']);

        $response = $this->post(route('posts.store'), [
            'title'       => 'Without Image',
            'description' => 'Body',
            'category_id' => $category->id,
        ]);

        $response->assertRedirect(route('posts.index'));
        $post = Post::first();
        $this->assertNull($post->image);
    }

    public function test_update_post_add_image(): void
    {
        Storage::fake('public');
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::USER]);
        $this->actingAs($user);
        /** @var Category $category */
        $category = Category::create(['name' => 'AddImage']);
        /** @var Post $post */
        $post = Post::create([
            'title'       => 'Initial',
            'description' => 'Body',
            'category_id' => $category->id,
            'user_id'     => $user->id,
        ]);

        $file     = UploadedFile::fake()->image('added.png');
        $response = $this->patch(route('posts.update', $post), [
            'title'       => 'Initial',
            'description' => 'Body',
            'category_id' => $category->id,
            'image'       => $file,
        ]);

        $response->assertRedirect(route('posts.index'));
        $post->refresh();
        $this->assertNotNull($post->image);
        $this->assertTrue(Storage::disk('public')->exists($post->image));
    }

    public function test_update_post_replace_image(): void
    {
        Storage::fake('public');
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::USER]);
        $this->actingAs($user);
        /** @var Category $category */
        $category = Category::create(['name' => 'ReplaceImage']);
        /** @var Post $post */
        $post = Post::create([
            'title'       => 'Initial',
            'description' => 'Body',
            'category_id' => $category->id,
            'user_id'     => $user->id,
        ]);

        $first = UploadedFile::fake()->image('first.png');
        $this->patch(route('posts.update', $post), [
            'title'       => 'Initial',
            'description' => 'Body',
            'category_id' => $category->id,
            'image'       => $first,
        ]);
        $post->refresh();
        $oldPath = $post->image;
        $this->assertTrue(Storage::disk('public')->exists($oldPath));

        $second = UploadedFile::fake()->image('second.png');
        $this->patch(route('posts.update', $post), [
            'title'       => 'Initial',
            'description' => 'Body',
            'category_id' => $category->id,
            'image'       => $second,
        ]);
        $post->refresh();
        $this->assertNotEquals($oldPath, $post->image);
        $this->assertTrue(Storage::disk('public')->exists($post->image));
        // Ainda não removemos o antigo automaticamente; melhoria futura seria apagar $oldPath.
    }
}
