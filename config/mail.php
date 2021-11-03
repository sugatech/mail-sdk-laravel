<?php declare(strict_types=1);

return [
    'api_url' => env('MAIL_API_URL'),
    'oauth' => [
        'url' => env('MAIL_OAUTH_URL', env('MAIL_API_URL').'/oauth/token'),
        'client_id' => env('MAIL_OAUTH_CLIENT_ID'),
        'client_secret' => env('MAIL_OAUTH_CLIENT_SECRET'),
    ],
];

