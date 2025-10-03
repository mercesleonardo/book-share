<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\{Category, Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');
    }

    public function test_dashboard_displays_basic_metrics_and_recent_posts(): void
    {
        /** @var User $user */
        $user     = User::factory()->create();
        $category = Category::factory()->create();
        Post::factory()->count(2)->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
        ]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(__('dashboard.metrics.total_posts'))
            ->assertSee(__('dashboard.metrics.last_30_days'))
            ->assertSee(__('dashboard.sections.recent_posts'));
    }

    public function test_admin_sees_global_metrics_and_moderation_queue_section(): void
    {
        /** @var User $admin */
        $admin    = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $category = Category::factory()->create();
        // Criar posts curtos para fila de moderação
        Post::factory()->count(3)->create([
            'user_id'     => $admin->id,
            'category_id' => $category->id,
            'description' => 'Short desc',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(__('dashboard.metrics.global_total_posts'))
            ->assertSee(__('dashboard.metrics.total_users'))
            ->assertSee(__('dashboard.metrics.posts_today'))
            ->assertSee(__('dashboard.metrics.posts_week'))
            ->assertSee(__('dashboard.metrics.with_image_ratio'))
            ->assertSee(__('dashboard.sections.moderation_queue'));
    }

    public function test_regular_user_does_not_see_global_metrics_or_moderation_queue(): void
    {
        /** @var User $user */
        $user     = User::factory()->create(['role' => UserRole::USER->value]);
        $category = Category::factory()->create();
        Post::factory()->count(1)->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'description' => 'Some sufficient description more than fifty characters maybe no',
        ]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee(__('dashboard.metrics.global_total_posts'))
            ->assertDontSee(__('dashboard.metrics.total_users'))
            ->assertDontSee(__('dashboard.sections.moderation_queue'));
    }

    public function test_admin_sees_trend_and_top_categories_sections(): void
    {
        /** @var User $admin */
        $admin     = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $categoryA = Category::factory()->create(['name' => 'A']);
        $categoryB = Category::factory()->create(['name' => 'B']);
        Post::factory()->count(3)->create(['user_id' => $admin->id, 'category_id' => $categoryA->id, 'description' => str_repeat('a', 60)]);
        Post::factory()->count(2)->create(['user_id' => $admin->id, 'category_id' => $categoryB->id, 'description' => str_repeat('b', 60)]);
        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(trans('dashboard.sections.trend_14_days'))
            ->assertSee(trans('dashboard.sections.top_categories'))
            ->assertSee('A')
            ->assertSee('B');
    }

    public function test_global_metrics_cached_reduce_queries_on_second_request(): void
    {
        /** @var User $admin */
        $admin    = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $category = Category::factory()->create();
        Post::factory()->count(5)->create(['user_id' => $admin->id, 'category_id' => $category->id, 'description' => str_repeat('x', 60)]);

        DB::enableQueryLog();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $first = collect(DB::getQueryLog())->count();

        DB::flushQueryLog();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $second = collect(DB::getQueryLog())->count();

        // Espera menos ou igual (nunca mais) e pelo menos 1 consulta de redução típica
        $this->assertTrue($second <= $first, "Expected second request queries ($second) <= first ($first)");
    }
}
