<?php

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'mysql',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_NAME') ?: 'alphacode_db',
        'username' => getenv('DB_USER') ?: 'alphacode_user',
        'password' => getenv('DB_PASSWORD') ?: 'alphacode_pass',
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'name' => 'Sistema de Pedidos AlphaCode',
        'url' => 'http://localhost:8080',
        'items_per_page' => 20
    ]
];
