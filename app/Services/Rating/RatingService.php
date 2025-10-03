<?php

namespace App\Services\Rating;

use App\Models\{Post, Rating};
use Illuminate\Support\Facades\{Cache};

class RatingService
{
    public function set(Post $post, int $userId, int $stars): bool
    {
        $existing = Rating::where('post_id', $post->id)->where('user_id', $userId)->exists();

        Rating::updateOrCreate(
            ['post_id' => $post->id, 'user_id' => $userId],
            ['stars' => $stars]
        );

        Cache::forget('post:ratings:avg:' . $post->id);
        Cache::forget('post:ratings:count:' . $post->id);

        return $existing;
    }
}
