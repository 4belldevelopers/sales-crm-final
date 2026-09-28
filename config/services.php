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

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'evolution' => [
        'url' => env('EVOLUTION_URL'),
        'global_key' => env('EVOLUTION_KEY'),
        'instance_pre' => env('EVOLUTION_INSTANCE_PRE'),
    ],

    // Cloudflare Turnstile — Sprint 2 task 2.4. Keys set via TURNSTILE_SITE_KEY / TURNSTILE_SECRET_KEY in .env
    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY', ''),
        'secret_key' => env('TURNSTILE_SECRET_KEY', ''),
    ],

    'phonepe' => [
        'merchant_id' => env('PHONEPE_MERCHANT_ID'),
        'api_key' => env('PHONEPE_API_KEY'),
        'key_index' => env('PHONEPE_API_KEY_INDEX'),
        'host_url' => env('PHONEPE_HOST_URL'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY', env('CHATGPT_API_KEY')),
        'key' => env('OPENAI_API_KEY', env('CHATGPT_API_KEY')),
    ],

    'zoom' => [
        'account_id' => env('ZOOM_ACCOUNT_ID'),
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
        'api_key' => env('ZOOM_API_KEY'),
        'api_secret' => env('ZOOM_API_SECRET'),
        'api_url' => env('ZOOM_API_URL', ''),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'soffice' => [
        'path' => env('SOFFICE_PATH'),
    ],

    'custom_encryption_key' => env('CUSTOM_ENCRYPTION_KEY'),

    'bench_v4' => [
        'enabled' => env('BENCH_V4_ENABLED', true),
        'use_data_v2' => env('BENCH_USE_DATA_V2', true),
        'enforce_durable_idempotency' => env('BENCH_DURABLE_IDEMPOTENCY', true),
        'enable_worker_parser' => env('BENCH_ENABLE_WORKER_PARSER', true),
    ],

];
