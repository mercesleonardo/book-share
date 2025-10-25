<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class GoogleSocialiteExistingProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_callback_logs_in_existing_provider_user(): void
    {
        $user = User::factory()->create([
            'email'         => 'existing@example.com',
            'provider_name' => 'google',
            'provider_id'   => 'google-555',
        ]);

        $socialiteUser         = new SocialiteUser();
        $socialiteUser->id     = 'google-555';
        $socialiteUser->name   = 'Existing User';
        $socialiteUser->email  = 'existing@example.com';
        $socialiteUser->avatar = 'https://example.com/avatar.jpg';

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }
}
