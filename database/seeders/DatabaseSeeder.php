<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\{Category, Post, User};
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Users
        /** @var User $admin */
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('password'),
                'role'              => UserRole::ADMIN,
                'email_verified_at' => now(),
                'password_set_at'   => now(),
            ]
        );

        /** @var User $moderator */
        $moderator = User::query()->firstOrCreate(
            ['email' => 'moderator@example.com'],
            [
                'name'              => 'Moderator',
                'password'          => Hash::make('password'),
                'role'              => UserRole::MODERATOR,
                'email_verified_at' => now(),
                'password_set_at'   => now(),
            ]
        );

        // Regular users
        $regularUsers = User::factory(8)->create();

        // Categories
        $categories = Category::factory(6)->create();

        // For each category, create posts by admin, moderator, and some regular users.
        $categories->each(function (Category $category) use ($admin, $moderator, $regularUsers): void {
            Post::factory(2)->create([
                'user_id'     => $admin->id,
                'category_id' => $category->id,
            ]);
            Post::factory(2)->create([
                'user_id'     => $moderator->id,
                'category_id' => $category->id,
            ]);
            // Choose 3 distinct random users for posts
            $regularUsers->random(3)->each(function (User $user) use ($category): void {
                Post::factory()->create([
                    'user_id'     => $user->id,
                    'category_id' => $category->id,
                ]);
            });
        });

        // After creating posts run additional seeders for ratings and comments
        $this->call([
            RatingSeeder::class,
            CommentSeeder::class,
        ]);
    }
}
