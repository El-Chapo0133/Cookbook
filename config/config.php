<?php

/**
 * App config for a small Raspberry Pi 3 PHP server.
 * Keep secrets out of public/. SQLite needs no extra daemon.
 */
return [
    'app' => [
        'name' => 'Livret de recettes',
        'base_path' => '',
    ],
    'db' => [
        'dsn' => 'sqlite:' . dirname(__DIR__) . '/data/cookbook.sqlite',
    ],
    'uploads' => [
        'dir' => dirname(__DIR__) . '/public/uploads/recipes',
        'url' => 'uploads/recipes',
        'max_bytes' => 8 * 1024 * 1024,
    ],
];
