<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_index(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->actingAs($admin);

        $response = $this->get(route('users.index', absolute: false));

        $response->assertOk();
    }

    public function test_regular_user_cannot_access_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::USER]);

        $this->actingAs($user);

        $response = $this->get(route('users.index', absolute: false));

        $response->assertForbidden();
    }

    public function test_regular_user_cannot_access_create_store(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::USER]);

        $this->actingAs($user);

        $this->get(route('users.create', absolute: false))->assertForbidden();

        $payload = [
            'name'  => 'John Doe',
            'email' => 'john@example.com',
            'role'  => UserRole::USER->value,
        ];

        $this->post(route('users.store', absolute: false), $payload)->assertForbidden();
    }

    public function test_regular_user_cannot_edit_update_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::USER]);
        /** @var User $target */
        $target = User::factory()->create(['role' => UserRole::USER]);

        $this->actingAs($user);

        $this->get(route('users.edit', ['user' => $target->id], absolute: false))->assertForbidden();

        $update = [
            'name'  => 'New Name',
            'email' => $target->email,
            'role'  => UserRole::USER->value,
        ];
        $this->patch(route('users.update', ['user' => $target->id], absolute: false), $update)->assertForbidden();

        $this->delete(route('users.destroy', ['user' => $target->id], absolute: false))->assertForbidden();
    }
}
