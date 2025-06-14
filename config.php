<?php
$env = parse_ini_file(__DIR__ . '/.env');

return [
    'stripe' => [
        'secret_key' => $env['STRIPE_SECRET']
    ],
    'db' => [
        'host' => $env['DB_HOST'],
        'user' => $env['DB_USER'],
        'pass' => $env['DB_PASS'],
        'name' => $env['DB_NAME']
    ],
    'telegram' => [
        'bot_token' => $env['BOT_TOKEN'],
        'chat_id' => $env['CHAT_ID']
    ]
];
