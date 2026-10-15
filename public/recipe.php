<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

$id = (int) ($_GET['id'] ?? 0);
$recipe = $id > 0 ? recipes()->findById($id) : null;

if ($recipe === null) {
    http_response_code(404);
    render('not_found', ['title' => 'Recette introuvable']);
    exit;
}

render('recipe', [
    'title' => $recipe->title,
    'recipe' => $recipe,
]);
