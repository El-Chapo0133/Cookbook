<div class="w-full max-w-5xl md:w-8/10 py-6 md:py-8 flex flex-col gap-8">
        <p class="text-center text-lg">
                <span class="inline-block rounded-full border-2 border-emerald-600 px-4 py-1 font-semibold">
                        <?= (int) $recipeCount === 1 ? '1 recette' : (int) $recipeCount . ' recettes' ?>
                </span>
        </p>
        <div>
                <h2 class="text-2xl font-bold mb-4">Dernières recettes</h2>
                <?php
                $recipes = $latestRecipes;
                $emptyMessage = 'Aucune recette pour le moment.';
                require __DIR__ . '/recipe_list.php';
                ?>
        </div>
        <?php if ((int) $recipeCount === 0): ?>
        <p class="text-center"><a href="<?= e(url('add_recipe.php')) ?>" class="text-emerald-700 hover:underline">Ajouter une recette</a></p>
        <?php endif; ?>
</div>
