<?php

declare(strict_types=1);

final class Unit
{
    public const G = 1;
    public const KG = 2;
    public const ML = 3;
    public const DL = 4;
    public const L = 5;
    public const PORTION = 6;
    public const CAS = 7;
    public const CAC = 8;

    /** @var array<int, string> */
    public const CODES = [
        self::G => 'g',
        self::KG => 'kg',
        self::ML => 'ml',
        self::DL => 'dl',
        self::L => 'l',
        self::PORTION => 'portion',
        self::CAS => 'C. à S.',
        self::CAC => 'C. à C.',
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
