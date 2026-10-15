<?php

declare(strict_types=1);

final class Unit
{
    public const G = 1;
    public const KG = 2;
    public const ML = 3;
    public const DL = 4;
    public const L = 5;

    /** @var array<int, string> */
    public const CODES = [
        self::G => 'g',
        self::KG => 'kg',
        self::ML => 'ml',
        self::DL => 'dl',
        self::L => 'l',
    ];

    public static function isValidId(int $id): bool
    {
        return isset(self::CODES[$id]);
    }

    public static function code(int $id): string
    {
        return self::CODES[$id] ?? self::CODES[self::G];
    }
}
