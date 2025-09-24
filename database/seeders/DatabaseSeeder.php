<?php

namespace Database\Seeders;

use App\Enums\UserRole;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name'            => 'Leonardo Carvalho',
            'email'           => 'leonardo@example.com',
            'role'            => UserRole::ADMIN,
            'description'     => 'Administrador do sistema',
            'password_set_at' => now(),
        ]);
    }
}
