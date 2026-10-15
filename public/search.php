<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

$q = trim((string) ($_GET['q'] ?? ''));
$by = (string) ($_GET['by'] ?? 'name');
$order = (string) ($_GET['order'] ?? 'created_at');
$searched = array_key_exists('q', $_GET) || array_key_exists('by', $_GET) || array_key_exists('order', $_GET);

if (!in_array($by, ['name', 'ingredients', 'tag'], true)) {
    $by = 'name';
}
if (!in_array($order, ['created_at', 'modified_at', 'duration'], true)) {
    $order = 'created_at';
}

$recipes = [];
if ($searched) {
    $recipes = recipes()->search($q, $by, $order);
}

render('search', [
    'title' => 'Recherche',
    'q' => $q,
    'by' => $by,
    'order' => $order,
    'searched' => $searched,
    'recipes' => $recipes,
]);
