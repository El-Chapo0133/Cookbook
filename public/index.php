<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

render('home', [
    'title' => $config['app']['name'],
    'recipeCount' => recipes()->count(),
    'latestRecipes' => recipes()->findLatest(4),
]);
