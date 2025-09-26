<?php

namespace Tests\Feature;

use App\Models\{Category, Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostCategoryModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_many_posts(): void
    {
        $category = Category::factory()->create();
        $posts    = Post::factory(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->posts);
        $expected = $posts->pluck('id')->sort()->values()->all();
        $actual   = $category->posts->pluck('id')->sort()->values()->all();
        $this->assertSame($expected, $actual);
    }

    public function test_post_belongs_to_category_and_user(): void
    {
        $post = Post::factory()->create();
        $this->assertInstanceOf(Category::class, $post->category);
        $this->assertInstanceOf(User::class, $post->user);
    }

    public function test_category_name_is_unique(): void
    {
        $name = 'Unique Category';
        Category::factory()->create(['name' => $name]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Category::factory()->create(['name' => $name]);
    }
}
