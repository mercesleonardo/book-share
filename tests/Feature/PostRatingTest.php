<?php

namespace Tests\Feature;

use App\Models\{Category, Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostRatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_rating_saved_on_post_creation(): void
    {
        /** @var User $user */
        $user     = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($user)
            ->post(route('posts.store'), [
                'title'       => 'Sample',
                'book_author' => 'Someone',
                'description' => 'Desc',
                'category_id' => $category->id,
                'user_rating' => 4,
            ])->assertRedirect();

        $this->assertDatabaseHas('posts', [
            'title'       => 'Sample',
            'user_rating' => 4,
            'category_id' => $category->id,
        ]);
    }

    public function test_other_user_can_rate_post_and_average_reflects(): void
    {
        /** @var User $author */
        $author = User::factory()->create();
        /** @var User $other */
        $other    = User::factory()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id'     => $author->id,
            'category_id' => $category->id,
            'user_rating' => 5,
        ]);

        $this->actingAs($other)
            ->post(route('posts.ratings.store', $post), [
                'stars' => 3,
            ])->assertRedirect();

        $this->assertDatabaseHas('ratings', [
            'post_id' => $post->id,
            'user_id' => $other->id,
            'stars'   => 3,
        ]);

        $this->assertEquals(3.0, $post->fresh()->community_average_rating);
        $this->assertEquals(1, $post->fresh()->community_ratings_count);
    }

    public function test_user_can_update_their_rating(): void
    {
        /** @var User $author */
        $author = User::factory()->create();
        /** @var User $other */
        $other    = User::factory()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id'     => $author->id,
            'category_id' => $category->id,
            'user_rating' => 2,
        ]);

        $this->actingAs($other)
            ->post(route('posts.ratings.store', $post), ['stars' => 2])
            ->assertRedirect();

        $this->actingAs($other)
            ->post(route('posts.ratings.store', $post), ['stars' => 5])
            ->assertRedirect();

        $this->assertEquals(5.0, $post->fresh()->community_average_rating);
        $this->assertEquals(1, $post->fresh()->community_ratings_count);
    }

    public function test_author_cannot_use_rating_endpoint(): void
    {
        /** @var User $author */
        $author   = User::factory()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id'     => $author->id,
            'category_id' => $category->id,
            'user_rating' => 3,
        ]);

        $this->actingAs($author)
            ->post(route('posts.ratings.store', $post), ['stars' => 4])
            ->assertRedirect(); // redirected back with error message

        $this->assertDatabaseMissing('ratings', [
            'post_id' => $post->id,
            'user_id' => $author->id,
        ]);
    }

    public function test_stars_validation_out_of_range_returns_422(): void
    {
        /** @var User $author */
        $author = User::factory()->create();
        /** @var User $other */
        $other    = User::factory()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id'     => $author->id,
            'category_id' => $category->id,
            'user_rating' => 5,
        ]);

        $this->actingAs($other)
            ->post(route('posts.ratings.store', $post), ['stars' => 0])
            ->assertStatus(302) // FormRequest redirects back
            ->assertSessionHasErrors('stars');

        $this->actingAs($other)
            ->post(route('posts.ratings.store', $post), ['stars' => 6])
            ->assertStatus(302)
            ->assertSessionHasErrors('stars');
    }

    public function test_average_with_multiple_users_rounds_one_decimal(): void
    {
        /** @var User $author */
        $author   = User::factory()->create();
        $category = Category::factory()->create();
        /** @var \Illuminate\Database\Eloquent\Collection<int,User> $raters */
        $raters = User::factory()->count(3)->create(); // 3 usuários

        $post = Post::factory()->create([
            'user_id'     => $author->id,
            'category_id' => $category->id,
            'user_rating' => 4,
        ]);

        $stars = [3, 4, 5]; // média = 4.0

        foreach ($raters as $idx => $user) {
            /** @var User $user */
            $this->actingAs($user)
                ->post(route('posts.ratings.store', $post), ['stars' => $stars[$idx]])
                ->assertRedirect();
        }

        $fresh = $post->fresh();
        $this->assertEquals(4.0, $fresh->community_average_rating);
        $this->assertEquals(3, $fresh->community_ratings_count);
    }
}
