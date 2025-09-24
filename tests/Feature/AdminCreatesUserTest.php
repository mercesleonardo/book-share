<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\InitialPasswordSetupNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminCreatesUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_creates_user_and_initial_setup_notification_is_sent(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('users.store', absolute: false), [
            'name'  => 'New Managed User',
            'email' => 'managed@example.com',
            'role'  => UserRole::USER->value,
        ]);

        $response->assertRedirect(route('users.index', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => 'managed@example.com',
            'name'  => 'New Managed User',
        ]);

        $user = User::whereEmail('managed@example.com')->first();
        $this->assertNotNull($user);

        // Confirma que a senha armazenada não é vazia (placeholder hash).
        $this->assertNotEmpty($user->getAuthPassword());

        Notification::assertSentTo($user, InitialPasswordSetupNotification::class);
    }
}
