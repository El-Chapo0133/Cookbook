<?php
$q = $q ?? '';
$by = $by ?? 'name';
$order = $order ?? 'created_at';
$searched = $searched ?? false;
$recipes = $recipes ?? [];
?>
<div class="w-full max-w-5xl md:w-8/10 py-6 md:py-8 flex flex-col gap-8">
        <form method="get" action="<?= e(url('search.php')) ?>" class="flex flex-col gap-4 items-center w-full">
                <input type="search" name="q" value="<?= e($q) ?>" placeholder="Rechercher une recette" class="border rounded-xl px-4 py-2 w-full max-w-xl text-center">
                <div class="flex flex-col sm:flex-row flex-wrap justify-center gap-3 sm:gap-6 w-full">
                        <label class="flex items-center justify-between sm:justify-center gap-2 w-full sm:w-auto">
                                Champ
                                <select name="by" class="border rounded-xl px-2 py-2 flex-1 sm:flex-none">
                                        <option value="name"<?= $by === 'name' ? ' selected' : '' ?>>Nom</option>
                                        <option value="ingredients"<?= $by === 'ingredients' ? ' selected' : '' ?>>Ingrédient</option>
                                        <option value="tag"<?= $by === 'tag' ? ' selected' : '' ?>>Étiquette</option>
                                </select>
                        </label>
                        <label class="flex items-center justify-between sm:justify-center gap-2 w-full sm:w-auto">
                                Trier par
                                <select name="order" class="border rounded-xl px-2 py-2 flex-1 sm:flex-none">
                                        <option value="created_at"<?= $order === 'created_at' ? ' selected' : '' ?>>Date d’ajout</option>
                                        <option value="modified_at"<?= $order === 'modified_at' ? ' selected' : '' ?>>Date de modification</option>
                                        <option value="duration"<?= $order === 'duration' ? ' selected' : '' ?>>Durée</option>
                                </select>
                        </label>
                </div>
                <button type="submit" class="rounded-2xl border-2 border-emerald-600 px-8 py-2 font-bold hover:bg-emerald-600 hover:text-white cursor-pointer">Rechercher</button>
        </form>

        <?php if ($searched): ?>
        <div>
                <?php
                $emptyMessage = 'Aucun résultat.';
                require __DIR__ . '/recipe_list.php';
                ?>
        </div>
        <?php endif; ?>
</div>
