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
                <link rel="stylesheet" href="<?= e(url('assets/css/app.css')) ?>">
                <?php foreach ($styles as $href): ?>
                <link rel="stylesheet" href="<?= e(url($href)) ?>">
                <?php endforeach; ?>
        </head>
        <body class="m-0">
                <div class="flex flex-col min-h-screen justify-between">
                        <header class="site-header flex justify-start items-center min-h-16 sticky w-full border-b-4 border-emerald-600 top-0 p-3 md:p-4 mb-2 bg-white z-10">
                                <a href="<?= e(url('index.php')) ?>" class="shrink-0"><img src="<?= e(url('assets/images/pot.png')) ?>" alt="Casserole" class="w-10 md:w-15 object-scale-down"></a>
                                <h1 class="text-xl sm:text-2xl md:text-4xl font-bold underline md:mr-5"><a href="<?= e(url('index.php')) ?>">Livret de recettes</a></h1>
                                <nav class="site-nav text-base md:text-lg w-full md:w-auto">
                                        <a class="hover:underline" href="<?= e(url('index.php')) ?>">Menu</a>
                                        <a class="hover:underline" href="<?= e(url('search.php')) ?>">Recherche</a>
                                        <a class="hover:underline" href="<?= e(url('add_recipe.php')) ?>">Ajout</a>
                                        <a class="hover:underline" href="<?= e(url('choose.php')) ?>">Choisi pour moi</a>
                                </nav>
                        </header>

                        <main class="flex justify-center flex-1 w-full px-3 md:px-0">
                                <?= $content ?>
                        </main>

                        <footer class="flex flex-col gap-3 p-4 md:grid md:grid-cols-5 md:min-h-30">
                                <p class="md:place-self-start md:self-center md:col-start-1 md:col-end-4">Autheur : <a href="mailto:levequeloris66@gmail.com" class="font-medium text-fg-brand hover:underline text-blue-800">Loris Lévêque</a><br>Licence : <a href="<?= e(url('license.php')) ?>" class="font-medium text-fg-brand hover:underline text-blue-800">MIT</a></p>
                                <p class="text-xs md:text-sm italic font-mono md:col-start-5 md:place-self-center"><?= e(last_update_date()) ?></p>
                        </footer>
                </div>
                <?php foreach ($scripts as $src): ?>
                <script src="<?= e(url($src)) ?>"></script>
                <?php endforeach; ?>
        </body>
</html>
