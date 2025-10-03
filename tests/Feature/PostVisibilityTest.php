<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\{Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(UserRole $role = UserRole::USER): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_regular_user_sees_only_own_posts(): void
    {
        $user  = $this->makeUser();
        $other = $this->makeUser();
        $mine  = Post::factory()->count(2)->create(['user_id' => $user->id]);
        Post::factory()->count(3)->create(['user_id' => $other->id]);

        $this->actingAs($user)
            ->get(route('admin.posts.index'))
            ->assertOk()
            ->assertSee($mine[0]->title)
            ->assertSee($mine[1]->title)
            ->assertDontSeeText(Post::where('user_id', $other->id)->first()->title);
    }

    public function test_admin_sees_all_posts(): void
    {
        $admin = $this->makeUser(UserRole::ADMIN);
        $user  = $this->makeUser();
        $posts = Post::factory()->count(2)->create(['user_id' => $user->id]);

        $this->actingAs($admin)
            ->get(route('admin.posts.index'))
            ->assertOk()
            ->assertSee($posts[0]->title)
            ->assertSee($posts[1]->title);
    }

    public function test_user_cannot_force_user_filter(): void
    {
        $user      = $this->makeUser();
        $other     = $this->makeUser();
        $mine      = Post::factory()->create(['user_id' => $user->id]);
        $otherPost = Post::factory()->create(['user_id' => $other->id]);

        $this->actingAs($user)
            ->get(route('admin.posts.index', ['user' => $other->id]))
            ->assertOk()
            ->assertSee($mine->title)
            ->assertDontSeeText($otherPost->title);
    }

    // Removed test that enforced 403 on viewing others' posts. Policy now allows any authenticated user to view.

    public function test_owner_can_view_own_post(): void
    {
        $user = $this->makeUser();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('posts.show', $post))
            ->assertOk()
            ->assertSee($post->title);
    }

    public function test_index_has_view_button_for_authorized_user(): void
    {
        $user = $this->makeUser();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('posts.index'))
            ->assertOk()
            ->assertSee(route('posts.show', $post), false);
    }
}
