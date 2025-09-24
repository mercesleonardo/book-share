<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetVerifiesEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_reset_marks_email_as_verified_if_unverified(): void
    {
        $user = User::factory()->unverified()->create([
            'password_set_at' => null,
        ]);

        $token = Password::createToken($user);

        $this->post(route('password.store', absolute: false), [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertRedirect(route('login', absolute: false));

        $user->refresh();

        $this->assertNotNull($user->email_verified_at, 'Email should be verified after password reset if it was previously unverified.');
        $this->assertNotNull($user->password_set_at, 'Password set timestamp should be stored.');
    }
}
