<?php

return [
    'base_uri' => 'https://api.nytimes.com/svc/books',
    'version' => env('NYT_API_VERSION', 3),
    'api_key' => env('NYT_API_KEY'),
    'paths' => [
        'books' => [
            'best_seller_history' => 'lists/best-sellers/history.json'
        ]
    ],
    'api_cache_enabled' => true,
    'cache_expiry' => 600,
    'cache_prefix' => [
        'best_seller' => 'best_seller_history'
    ]
];
