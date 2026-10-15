<?php

declare(strict_types=1);

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        global $config;

        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $dsn = (string) $config['db']['dsn'];
        if (str_starts_with($dsn, 'sqlite:')) {
            $path = substr($dsn, strlen('sqlite:'));
            $dir = dirname($path);
            if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
                throw new RuntimeException('Impossible de créer le dossier des données.');
            }
        }

        self::$pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        self::$pdo->exec('PRAGMA foreign_keys = ON');
        self::migrate(self::$pdo);

        return self::$pdo;
    }

    private static function migrate(PDO $pdo): void
    {
        $file = __DIR__ . '/schema.sql';
        $sql = (string) file_get_contents($file);
        $sql = preg_replace('/--.*$/m', '', $sql) ?? $sql;

        foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
            $pdo->exec($statement);
        }

        $columns = $pdo->query('PRAGMA table_info(recipes)')->fetchAll();
        $names = array_column($columns, 'name');
        if (!in_array('modified_at', $names, true)) {
            $pdo->exec('ALTER TABLE recipes ADD COLUMN modified_at TEXT NOT NULL DEFAULT ""');
            $pdo->exec("UPDATE recipes SET modified_at = created_at WHERE modified_at = ''");
        }
    }
}
