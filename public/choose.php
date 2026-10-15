<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

$id = recipes()->findRandomId();

if ($id !== null) {
    redirect('recipe.php?id=' . $id);
}

render('choose', [
    'title' => 'Choisi pour moi',
]);
