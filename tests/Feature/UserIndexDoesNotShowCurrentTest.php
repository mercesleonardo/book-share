<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserIndexDoesNotShowCurrentTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_not_listed(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $other = User::factory()->create(['name' => 'Other Person', 'email' => 'other@example.com']);

        $this->actingAs($admin);

        $response = $this->get(route('users.index', absolute: false));

        $response->assertStatus(200);
        $response->assertSee('Other Person');

        // Captura apenas o HTML da tabela para garantir que o email do usuário autenticado
        // não aparece na listagem (pode aparecer no layout/nav, o que é aceitável).
        preg_match('/<table.*?<\/table>/s', $response->getContent(), $matches);
        $tableHtml = $matches[0] ?? '';

        $this->assertNotEmpty($tableHtml, 'User table HTML was not found in the response.');
        $this->assertStringNotContainsString($admin->email, $tableHtml, 'Authenticated user email should not appear in users table.');
    }
}
