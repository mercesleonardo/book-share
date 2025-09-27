<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleHelpersTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_admin_helper(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isModerator());
    }

    public function test_is_moderator_helper(): void
    {
        $moderator = User::factory()->create(['role' => UserRole::MODERATOR->value]);
        $this->assertTrue($moderator->isModerator());
        $this->assertFalse($moderator->isAdmin());
    }

    public function test_regular_user_helpers(): void
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $this->assertFalse($user->isModerator());
        $this->assertFalse($user->isAdmin());
    }
}
