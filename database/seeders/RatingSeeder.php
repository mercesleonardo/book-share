<?php

namespace Database\Seeders;

use App\Models\{Post, Rating, User};
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->count() < 2) {
            return; // need multiple users for community ratings
        }

        Post::query()->inRandomOrder()->take(40)->get()->each(function (Post $post) use ($users): void {
            // pick between 2 e 6 distinct users different from author
            $raters = $users->where('id', '!=', $post->user_id)->shuffle()->take(rand(2, 6));
            foreach ($raters as $user) {
                // avoid duplicate by unique constraint
                Rating::query()->firstOrCreate([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                ], [
                    'stars' => rand(1, 5),
                ]);
            }
        });
    }
}
