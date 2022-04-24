<?php

$DB_HOST = $_ENV['DB_HOST'];
$DB_NAME = $_ENV['DB_NAME'];
$DB_USER = $_ENV['DB_USER'];
$DB_PASS = $_ENV['DB_PASS'];

return [
    'database' => [
        'connection' => "mysql:host=$DB_HOST",
        'name' => $DB_NAME,
        'username' => $DB_USER,
        'password' => $DB_PASS,
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
