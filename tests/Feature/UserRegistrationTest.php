<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_registered_with_profile_photo_and_description(): void
    {
        Storage::fake('public');

        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'testuser@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
            'profile_photo'         => UploadedFile::fake()->image('avatar.jpg'),
            'description'           => 'Descrição de teste',
            'role'                  => 'user',
        ]);

        $response->assertRedirect(route('admin.dashboard', absolute: false));
        $this->assertAuthenticated();

        $user = User::where('email', 'testuser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Descrição de teste', $user->description);
        $this->assertTrue(Storage::disk('public')->exists($user->profile_photo));
    }

    public function test_user_can_be_registered_without_profile_photo_and_description(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'No Photo User',
            'email'                 => 'nophoto@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
            'role'                  => 'user',
        ]);

        $response->assertRedirect(route('admin.dashboard', absolute: false));
        $this->assertAuthenticated();

        $user = User::where('email', 'nophoto@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->profile_photo);
        $this->assertNull($user->description);
    }
}
