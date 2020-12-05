<?php

return [
    'database' => [
        'connection' => 'mysql:host=127.0.0.1',
        'name' => 'WWW',
        'username' => 'root',
        'password' => '',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ],
    ],
    'cookies' => [
        'expiry' => [
            'SHORT' => '1 hour',
            'MEDIUM' => '1 day',
            'LONG' => '14 days'
        ]
    ],
    'timezone' => 'Europe/Vilnius'
];
