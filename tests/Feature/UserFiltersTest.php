<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_by_name(): void
    {
        $admin   = User::factory()->create(['role' => UserRole::ADMIN]);
        $match   = User::factory()->create(['name' => 'Alice Johnson']);
        $noMatch = User::factory()->create(['name' => 'Bob Smith']);

        $response = $this->actingAs($admin)
            ->get(route('admin.users.index', ['name' => 'Alice']));

        $response->assertOk();
        $response->assertSee($match->name);
        $response->assertDontSee($noMatch->name);
    }

    public function test_admin_can_filter_by_role(): void
    {
        $admin     = User::factory()->create(['role' => UserRole::ADMIN]);
        $moderator = User::factory()->create(['role' => UserRole::MODERATOR]);
        $user      = User::factory()->create(['role' => UserRole::USER]);

        $response = $this->actingAs($admin)
            ->get(route('admin.users.index', ['role' => UserRole::MODERATOR->value]));

        $response->assertOk();
        $response->assertSee($moderator->email);
        $response->assertDontSee($user->email);
    }
}
