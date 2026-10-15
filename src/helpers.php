<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function char_len(string $value): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($value, 'UTF-8');
    }

    return strlen($value);
}

function url(string $path): string
{
    global $config;

    $base = rtrim((string) ($config['app']['base_path'] ?? ''), '/');
    $path = ltrim($path, '/');

    return ($base === '' ? '' : $base) . '/' . $path;
}

function format_quantity(float $quantity): string
{
    if (fmod($quantity, 1.0) === 0.0) {
        return (string) (int) $quantity;
    }

    return rtrim(rtrim(number_format($quantity, 2, ',', ' '), '0'), ',');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function like_pattern(string $query): string
{
    $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $query);

    return '%' . $escaped . '%';
}

function last_update_date(): string
{
    $file = dirname(__DIR__) . '/data/last_update_date.txt';
    if (!is_file($file)) {
        return '';
    }

    return trim((string) file_get_contents($file));
}
