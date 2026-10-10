<?php

return [
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'laravel_backend' => [
        'url' => env('LARAVEL_BACKEND_URL'),
        'api_key' => env('LARAVEL_BACKEND_API_KEY'),
        'connect_timeout' => (int) env('LARAVEL_BACKEND_CONNECT_TIMEOUT', 5),
        'timeout' => (int) env('LARAVEL_BACKEND_TIMEOUT', 15),

        // Longest suffix wins. These codes describe the existing laravelBackend
        // game-directory convention; they are not the removed 336699bet catalog.
        'provider_suffixes' => [
            'DXGTM' => 'GT Mobile',
            'DXGT' => 'GT',
            'JPPTM' => 'Playtech Mobile',
            'JPPT' => 'Playtech',
            '10000VP' => 'VP',
            'PTM' => 'Playtech Mobile',
            'PMM' => 'Pragmatic Mobile',
            'GTM' => 'GT Mobile',
            'NET' => 'NetEnt',
            'CQ9' => 'CQ9',
            'PGD' => 'PGD',
            'PGT' => 'PGT',
            'ISB' => 'ISB',
            'EGT' => 'EGT',
            'AM' => 'Amatic',
            'AT' => 'Aristocrat',
            'BS' => 'BS',
            'CT' => 'CT',
            'GM' => 'GM',
            'GT' => 'GT',
            'KA' => 'KA Gaming',
            'MN' => 'MN',
            'NG' => 'NG',
            'PM' => 'Pragmatic',
            'PT' => 'Playtech',
            'VP' => 'VP',
            'WD' => 'WD',
            'VS' => 'VS',
            'SW' => 'SW',
            'IG' => 'IG',
            'PG' => 'PG',
            'SG' => 'SG',
        ],
    ],
];
