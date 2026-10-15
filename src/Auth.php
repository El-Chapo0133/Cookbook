<?php

declare(strict_types=1);

/**
 * Placeholder for later session login. Pages can call Auth::check() without
 * changing once real auth is added.
 */
final class Auth
{
    public static function user(): ?array
    {
        return null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }
}
