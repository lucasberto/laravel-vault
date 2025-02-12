<?php

return [
    'default' => 'main',
    
    'servers' => [
        'main' => [
            'address' => env('VAULT_ADDR', 'http://127.0.0.1:8200'),
            'token' => env('VAULT_TOKEN'),
            'version' => env('VAULT_VERSION', 'v1'),
            'timeout' => env('VAULT_TIMEOUT', 30),
        ],
        'secondary' => [
            'address' => env('VAULT_SECONDARY_ADDR'),
            'token' => env('VAULT_SECONDARY_TOKEN'),
            'version' => env('VAULT_SECONDARY_VERSION', 'v1'),
            'timeout' => env('VAULT_SECONDARY_TIMEOUT', 30),
        ],
    ],
];