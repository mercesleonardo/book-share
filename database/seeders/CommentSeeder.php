<?php

namespace Database\Seeders;

use App\Models\{Comment, Post, User};
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }

        Post::query()->with('user')->inRandomOrder()->take(60)->get()->each(function (Post $post) use ($users): void {
            // each selected post receives between 1 and 5 comments
            $count = rand(1, 5);
            for ($i = 0; $i < $count; $i++) {
                $author = $users->random();
                Comment::factory()->create([
                    'post_id' => $post->id,
                    'user_id' => $author->id,
                ]);
            }
        });
    }
}
