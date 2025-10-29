<?php

namespace App\Services\Moderation;

use Illuminate\Support\Facades\{Http, Log};

class OpenAIModerationService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    public function moderate(string $input): bool
    {
        try {
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
