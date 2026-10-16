<?php

/**
 * App config for a small Raspberry Pi 3 PHP server.
 * Keep secrets out of public/. SQLite needs no extra daemon.
 *
 * Local: files live in ./public
 * Docker: the same files are served from /var/www/html
 */
$root = dirname(__DIR__);
$public = is_dir($root . '/public') ? $root . '/public' : $root . '/html';
$uploadDir = getenv('COOKBOOK_UPLOAD_DIR') ?: ($public . '/uploads/recipes');

return [
    'app' => [
        'name' => 'Livret de recettes',
        'base_path' => '',
    ],
    'db' => [
        'dsn' => 'sqlite:' . $root . '/data/cookbook.sqlite',
    ],
    'uploads' => [
        'dir' => $uploadDir,
        'url' => 'uploads/recipes',
        'max_bytes' => 8 * 1024 * 1024,
    ],
];
