<?php

namespace App\Services\Moderation;

use App\Models\Post;
use Illuminate\Support\Facades\{Http, Log, Cache};

class OpenAIModerationService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    /**
     * Moderate a Post using OpenAI Moderation API.
     *
     * The service is responsible for composing the input from the Post fields
     * (title, book_author, description) and applying a safe size limit before
     * sending to OpenAI.
     */
    public function moderate(Post $post): bool
    {
        try {
            $parts = [];

            if (!empty($post->title)) {
                $parts[] = 'Title: ' . $post->title;
            }

            if (!empty($post->book_author)) {
                $parts[] = 'Book author: ' . $post->book_author;
            }

            if (!empty($post->description)) {
                $parts[] = 'Description: ' . $post->description;
            }

            $input = implode("\n\n", $parts);

            $max = config('services.openai.moderation_input_max', 3000);

            if (mb_strlen($input) > $max) {
                $input = mb_substr($input, 0, $max);
            }

            // Cache key based on the input text
            $key = 'moderation:' . sha1($input);

            // Short-circuit if there's a recent failure to avoid stampede
            $failureKey = $key . ':failure';
            if (Cache::has($failureKey)) {
                return false;
            }

            // Try cache first
            $cached = Cache::get($key);
            if ($cached !== null) {
                return (bool) $cached;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->post('https://api.openai.com/v1/moderations', [
                'model' => 'omni-moderation-latest',
                'input' => $input,
            ]);

            if ($response->failed()) {
                Log::error('OpenAI Moderation API failed', ['response' => $response->body()]);

                // cache a short failure marker to avoid repeated immediate retries
                $failureTtl = config('services.openai.moderation_failure_cache_minutes', 2);
                Cache::put($failureKey, true, now()->addMinutes($failureTtl));

                return false;
            }

            $result = $response->json();

            $safe = !($result['results'][0]['flagged'] ?? false);

            // store the boolean verdict in cache
            $ttl = config('services.openai.moderation_cache_ttl', 60 * 60 * 24); // seconds
            Cache::put($key, $safe, now()->addSeconds($ttl));

            return $safe;

        } catch (\Throwable $e) {
            Log::error('OpenAI Moderation Exception', ['message' => $e->getMessage()]);

            return false;
        }
    }
}
