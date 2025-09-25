<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRestoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_restore_soft_deleted_user(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        /** @var User $target */
        $target = User::factory()->create(['role' => UserRole::USER]);

        // Soft delete the target user
        $target->delete();
        $this->assertSoftDeleted($target);

        $this->actingAs($admin);

        $response = $this->patch(route('users.restore', ['user' => $target->id], absolute: false));

        $response->assertRedirect(route('users.index', absolute: false));
        $this->assertNull($target->fresh()->deleted_at);
    }

    public function test_regular_user_cannot_restore_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::USER]);
        /** @var User $target */
        $target = User::factory()->create(['role' => UserRole::USER]);

        $target->delete();
        $this->assertSoftDeleted($target);

        $this->actingAs($user);

        $this->patch(route('users.restore', ['user' => $target->id], absolute: false))
            ->assertForbidden();

        // Ensure still soft-deleted
        $this->assertSoftDeleted($target->fresh());
    }
}
