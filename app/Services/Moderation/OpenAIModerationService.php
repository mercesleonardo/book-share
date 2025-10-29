<?php

namespace App\Services\Moderation;

use App\Models\Post;
use Illuminate\Support\Facades\{Http, Log};

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

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->post('https://api.openai.com/v1/moderations', [
                'model' => 'omni-moderation-latest',
                'input' => $input,
            ]);

            if ($response->failed()) {
                Log::error('OpenAI Moderation API failed', ['response' => $response->body()]);

                return false;
            }

            $result = $response->json();

            return !($result['results'][0]['flagged'] ?? false);

        } catch (\Throwable $e) {
            Log::error('OpenAI Moderation Exception', ['message' => $e->getMessage()]);

            return false;
        }
    }
}
