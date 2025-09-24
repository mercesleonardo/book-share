<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\InitialPasswordSetupNotification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Notification, Password};
use Tests\TestCase;

class PasswordSetupFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_created_user_starts_without_password_set_at(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin);

        $this->post(route('users.store', absolute: false), [
            'name'  => 'Managed User',
            'email' => 'managed2@example.com',
            'role'  => UserRole::USER->value,
        ])->assertRedirect(route('users.index', absolute: false));

        $user = User::whereEmail('managed2@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->password_set_at, 'password_set_at should be null until user sets password');
        Notification::assertSentTo($user, InitialPasswordSetupNotification::class);
    }

    public function test_password_set_at_marked_when_user_resets_password(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'role'            => UserRole::USER,
            'password_set_at' => null,
        ]);

        // Solicita reset
        Password::sendResetLink(['email' => $user->email]);
        Notification::assertSentTo($user, ResetPassword::class);

        // Simula fluxo de reset real
        $token = Password::createToken($user);
        $this->post(route('password.store', absolute: false), [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'newStrongPass1!',
            'password_confirmation' => 'newStrongPass1!',
        ])->assertRedirect(route('login', absolute: false));

        $user->refresh();
        $this->assertNotNull($user->password_set_at, 'password_set_at should be set after password reset');
    }

    public function test_public_registration_sets_password_set_at_immediately(): void
    {
        $this->post('/register', [
            'name'                  => 'Public User',
            'email'                 => 'public@example.com',
            'password'              => 'password1234',
            'password_confirmation' => 'password1234',
        ])->assertRedirect(route('dashboard', absolute: false));

        $user = User::whereEmail('public@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->password_set_at);
    }
}
