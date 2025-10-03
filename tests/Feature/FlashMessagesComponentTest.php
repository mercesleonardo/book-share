<?php

namespace Tests\Feature;

use App\Models\{User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashMessagesComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_displays_single_success_message(): void
    {
        $user = User::factory()->create();
        /** @var User $user */
        $response = $this->actingAs($user)->withSession(['success' => 'Saved OK'])->get(route('admin.dashboard'));
        $response->assertSee('Saved OK');
    }

    public function test_displays_multiple_errors_as_stack(): void
    {
        $user = User::factory()->create();
        /** @var User $user */
        $response = $this->actingAs($user)->withSession([
            'error' => ['First error', 'Second error'],
        ])->get(route('admin.dashboard'));

        $response->assertSee('First error');
        $response->assertSee('Second error');
    }

    public function test_displays_validation_errors_block(): void
    {
        $user = User::factory()->create();
        /** @var User $user */
        // Simula erros de validação adicionando manualmente bag
        $response = $this->actingAs($user)->withSession([
            'errors' => session()->get('errors') ?? tap(new \Illuminate\Support\ViewErrorBag(), function ($bag) {
                $messageBag = new \Illuminate\Support\MessageBag([
                    'field' => ['Campo inválido'],
                ]);
                $bag->put('default', $messageBag);
            }),
        ])->get(route('admin.dashboard'));

        $response->assertSee(__('validation.errors_title'));
        $response->assertSee('Campo inválido');
    }

    public function test_displays_status_message_as_info(): void
    {
        $user = User::factory()->create();
        /** @var User $user */
        $response = $this->actingAs($user)->withSession(['status' => 'Info message'])->get(route('admin.dashboard'));
        $response->assertSee('Info message');
    }
}
