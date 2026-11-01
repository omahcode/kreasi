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

    'wargarpl' => [
        'base_url' => env('WARGARPL_BASE_URL', 'https://wargarpl.neperone.id'),
        'token' => env('WARGARPL_API_TOKEN', 'wgr_live_ElQjmkVoe57e9EmV306v9RHktS7OeHjSoKsyDpcQ'),
        'student_email_domain' => env('WARGARPL_STUDENT_EMAIL_DOMAIN', 'siswa.kreasi.test'),
    ],

];
