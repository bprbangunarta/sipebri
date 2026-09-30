<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    /*
    | Codex. Two uses share one endpoint and one set of credentials:
    |  - sign-in: the login form is checked against POST {endpoint}/api/web-auth (bearer CODEX_TOKEN);
    |  - customer master: applicant identity is looked up by national ID (OAuth client_credentials with CODEX_ID / CODEX_SECRET).
    | When CODEX_ENDPOINT is empty the customer lookup falls back to employee records for local use.
    */
    'codex' => [
        'endpoint' => env('CODEX_ENDPOINT'),
        'token' => env('CODEX_TOKEN'),
        'id' => env('CODEX_ID'),
        'secret' => env('CODEX_SECRET'),
        'verify' => env('CODEX_VERIFY', true),
        'timeout' => (float) env('CODEX_TIMEOUT', 10),
        'connect_timeout' => (float) env('CODEX_CONNECT_TIMEOUT', 5),
        'token_skew' => (int) env('CODEX_TOKEN_SKEW', 300),
    ],

];
