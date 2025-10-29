<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT'),
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        // Maximum characters to send to the moderation endpoint when composing
        // title, author and description. Configurable via env.
        'moderation_input_max' => env('OPENAI_MODERATION_INPUT_MAX', 3000),
        // Cache TTL (in seconds) for moderation verdicts. Default: 1 day.
        'moderation_cache_ttl' => env('OPENAI_MODERATION_CACHE_TTL', 86400),
        // Failure cache TTL (in minutes) to avoid retry stampede when API fails.
        'moderation_failure_cache_minutes' => env('OPENAI_MODERATION_FAILURE_CACHE_MINUTES', 2),
    ],
];
