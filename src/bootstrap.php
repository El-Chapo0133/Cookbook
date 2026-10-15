<?php

declare(strict_types=1);

$config = require dirname(__DIR__) . '/config/config.php';

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Unit.php';
require_once __DIR__ . '/ImageUploader.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Recipe.php';
require_once __DIR__ . '/RecipeRepository.php';
require_once __DIR__ . '/Auth.php';

function image_uploader(): ImageUploader
{
    global $config;

    return new ImageUploader(
        $config['uploads']['dir'],
        $config['uploads']['url'],
        $config['uploads']['max_bytes'],
    );
}

function recipes(): RecipeRepository
{
    static $repository = null;

    if ($repository === null) {
        $repository = new RecipeRepository(Database::pdo());
    }

    return $repository;
}

/**
 * Render a template inside the shared layout.
 *
 * @param array<string, mixed> $data
 */
function render(string $view, array $data = []): void
{
    global $config;

    $viewFile = dirname(__DIR__) . '/templates/' . $view . '.php';
    if (!is_file($viewFile)) {
        http_response_code(500);
        echo 'Vue introuvable.';
        return;
    }

    extract($data, EXTR_SKIP);

    ob_start();
    require $viewFile;
    $content = ob_get_clean();

    require dirname(__DIR__) . '/templates/layout.php';
}
