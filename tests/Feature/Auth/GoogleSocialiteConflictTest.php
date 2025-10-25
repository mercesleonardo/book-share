<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class GoogleSocialiteConflictTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_callback_throws_when_email_conflicts_with_existing_user(): void
    {
        // existing local user using the same email (but not linked to provider)
        User::factory()->create(['email' => 'jane@example.com']);

        $socialiteUser        = new SocialiteUser();
        $socialiteUser->id    = 'google-999';
        $socialiteUser->name  = 'Jane Doe';
        $socialiteUser->email = 'jane@example.com';

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/login');
        $this->assertEquals(session('error'), __('This email is already in use. Please log in with your existing account.'));
    }
}
