<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class GoogleSocialiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_callback_creates_user_and_logs_in(): void
    {
        // Prepare a fake Socialite user
        $socialiteUser         = new SocialiteUser();
        $socialiteUser->id     = 'google-12345';
        $socialiteUser->name   = 'Jane Doe';
        $socialiteUser->email  = 'jane@example.com';
        $socialiteUser->avatar = 'https://example.com/avatar.jpg';

        // Mock the Socialite facade chain driver('google')->user()
        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/');

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email'         => 'jane@example.com',
            'provider_name' => 'google',
            'provider_id'   => 'google-12345',
        ]);
    }
}
