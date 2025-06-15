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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'telegram' => [
        'bot' => env('TELEGRAM_BOT_NAME'),
        'client_id' => null,
        'client_secret' => env('TELEGRAM_BOT_TOKEN'),
        'redirect' => env('TELEGRAM_AUTH_REDIRECT'),
    ],
    'h_captcha' => [
        'secret' => env('H_CAPTCHA_SECRET'),
        'sitekey' => env('H_CAPTCHA_SITEKEY'),
    ],
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
    ]

];
