<?php
/** @var string $content */
$title = $title ?? ($config['app']['name'] ?? 'Livret de Recette');
$styles = $styles ?? [];
$scripts = $scripts ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
        <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title><?= e($title) ?></title>
                <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
                <?php foreach ($styles as $href): ?>
                <link rel="stylesheet" href="<?= e(url($href)) ?>">
                <?php endforeach; ?>
        </head>
        <body class="m-0">
                <div class="flex flex-col min-h-screen justify-between">
                        <header class="flex justify-start gap-10 items-center h-1/10 min-h-20 sticky w-full border-b-4 border-emerald-600 top-0 p-4 mb-2 bg-white z-10">
                                <a href="<?= e(url('index.php')) ?>"><img src="<?= e(url('assets/images/pot.png')) ?>" alt="Casserole" class="w-15 object-scale-down"></a>
                                <h1 class="text-4xl font-bold underline mr-5"><a href="<?= e(url('index.php')) ?>">Livret de recettes</a></h1>
                                <p class="text-lg hover:underline"><a href="<?= e(url('index.php')) ?>">Menu</a></p>
                                <p class="text-lg hover:underline"><a href="<?= e(url('search.php')) ?>">Recherche</a></p>
                                <p class="text-lg hover:underline"><a href="<?= e(url('add_recipe.php')) ?>">Ajout</a></p>
                                <p class="text-lg hover:underline"><a href="<?= e(url('choose.php')) ?>">Choisi pour moi</a></p>
                                <!--<p class="text-lg hover:underline ml-auto">login</p>-->
                        </header>

                        <main class="flex justify-center flex-1">
                                <?= $content ?>
                        </main>

                        <footer class="min-h-30 grid grid-cols-5 p-4">
                                <p class="place-self-start self-center col-start-1 col-end-4">Autheur : <a href="mailto:levequeloris66@gmail.com" class="font-medium text-fg-brand hover:underline text-blue-800">Loris Lévêque</a><br>Licence : <a href="<?= e(url('licence.php')) ?>" class="font-medium text-fg-brand hover:underline text-blue-800">MIT</a></p>
                                <p class="text-sm italic font-mono col-start-5 place-self-center"><?= e(last_update_date()) ?></p>
                        </footer>
                </div>
                <?php foreach ($scripts as $src): ?>
                <script src="<?= e(url($src)) ?>"></script>
                <?php endforeach; ?>
        </body>
</html>
