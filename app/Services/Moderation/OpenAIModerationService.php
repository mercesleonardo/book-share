<?php

namespace App\Services\Moderation;

use App\Models\Post;
use Illuminate\Support\Facades\{Cache, Http, Log};

class OpenAIModerationService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    /**
     * Moderate content using OpenAI Moderation API.
     *
     * Accepts either an instance of `Post` (will compose title/author/description)
     * or a raw string (for comments or other free text). The service composes
     * the input, enforces a size limit, consults cache and calls OpenAI when
     * needed.
     *
     * @param Post|string $input
     */
    public function moderate(Post|string $input): bool
    {
        $useCache = false;

        try {
            // Build a single text input regardless of whether we received a
            // Post or a raw string.
            if ($input instanceof Post) {
                $parts = [];

                if (!empty($input->title)) {
                    $parts[] = 'Title: ' . $input->title;
                }

                if (!empty($input->book_author)) {
                    $parts[] = 'Book author: ' . $input->book_author;
                }

                if (!empty($input->description)) {
                    $parts[] = 'Description: ' . $input->description;
                }

                $text = implode("\n\n", $parts);
            } else {
                // raw string (e.g. comment body)
                $text = (string) $input;
            }

            $max = config('services.openai.moderation_input_max', 3000);

            if (mb_strlen($text) > $max) {
                $text = mb_substr($text, 0, $max);
            }

            $key = 'moderation:' . sha1($text);

            $failureKey = $key . ':failure';

            // During automated tests we avoid caching to prevent cross-test
            // pollution (phpunit uses an in-memory array cache that persists
            // for the process). In production/staging caching is enabled.
            $useCache = !app()->runningUnitTests() && config('cache.default') !== 'array';

            if ($useCache) {
                if (Cache::has($failureKey)) {
                    return false;
                }

                $cached = Cache::get($key);

                if ($cached !== null) {
                    return (bool) $cached;
                }
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->post('https://api.openai.com/v1/moderations', [
                'model' => 'omni-moderation-latest',
                'input' => $text,
            ]);

            if ($response->failed()) {
                Log::error('OpenAI Moderation API failed', ['response' => $response->body()]);
                $failureTtl = config('services.openai.moderation_failure_cache_minutes', 2);
                if ($useCache) {
                    Cache::put($failureKey, true, now()->addMinutes($failureTtl));
                }

                return false;
            }

            $result = $response->json();
            $safe = !($result['results'][0]['flagged'] ?? false);
            // Log debug information to help trace discrepancies between
            // service result and controller usage.
            Log::debug('OpenAI moderation result', [
                'key' => $key,
                'useCache' => $useCache,
                'safe' => $safe,
                'response_summary' => $result['results'][0] ?? null,
            ]);
            if ($useCache) {
                $ttl = config('services.openai.moderation_cache_ttl', 60 * 60 * 24); // seconds
                Cache::put($key, $safe, now()->addSeconds($ttl));
            }

            return $safe;

        } catch (\Throwable $e) {
            Log::error('OpenAI Moderation Exception', ['message' => $e->getMessage()]);

            return false;
        }
    }
}
