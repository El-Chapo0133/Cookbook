<?php
/** @var Recipe $recipe */
?>
<article class="w-full max-w-5xl md:w-8/10 py-6 md:py-8 flex flex-col gap-8">
        <header class="flex flex-col items-center gap-3 text-center">
                <h2 class="text-2xl md:text-4xl font-bold break-words"><?= e($recipe->title) ?></h2>
                <?php if ($recipe->imagePath): ?>
                <img src="<?= e(url($recipe->imagePath)) ?>" alt="<?= e($recipe->title) ?>" class="max-h-80 w-full object-contain">
                <?php endif; ?>
                <p class="text-stone-600">
                        <?php if ($recipe->people > 0): ?>
                        <?= $recipe->people === 1 ? '1 personne' : e((string) $recipe->people) . ' personnes' ?>
                        <?php endif; ?>
                        <?php if ($recipe->people > 0 && $recipe->duration > 0): ?> · <?php endif; ?>
                        <?php if ($recipe->duration > 0): ?>
                        <?= e((string) $recipe->duration) ?> min
                        <?php endif; ?>
                </p>
                <?php if ($recipe->tags !== []): ?>
                <div class="flex flex-wrap justify-center gap-2">
                        <?php foreach ($recipe->tags as $tag): ?>
                        <a href="<?= e(url('search.php?q=' . rawurlencode($tag) . '&by=tag')) ?>" class="border rounded-xl px-3 py-1 hover:bg-emerald-50"><?= e($tag) ?></a>
                        <?php endforeach; ?>
                </div>
                <?php endif; ?>
        </header>

        <section>
                <h3 class="text-2xl font-semibold mb-3">Ingrédients</h3>
                <?php if ($recipe->ingredients === []): ?>
                <p class="text-stone-500">Aucun ingrédient.</p>
                <?php else: ?>
                <ul class="list-disc pl-6 flex flex-col gap-1">
                        <?php foreach ($recipe->ingredients as $ingredient): ?>
                        <li><?= e(format_ingredient($ingredient)) ?></li>
                        <?php endforeach; ?>
                </ul>
                <?php endif; ?>
        </section>

        <section>
                <h3 class="text-2xl font-semibold mb-3">Marche à suivre</h3>
                <?php if ($recipe->steps === []): ?>
                <p class="text-stone-500">Aucune étape.</p>
                <?php else: ?>
                <ol class="list-decimal pl-6 flex flex-col gap-3">
                        <?php foreach ($recipe->steps as $step): ?>
                        <li class="whitespace-pre-wrap"><?= e($step) ?></li>
                        <?php endforeach; ?>
                </ol>
                <?php endif; ?>
        </section>

        <?php if ($recipe->note !== ''): ?>
        <section>
                <h3 class="text-2xl font-semibold mb-3">Note</h3>
                <p class="whitespace-pre-wrap border rounded-xl p-4 bg-stone-50"><?= e($recipe->note) ?></p>
        </section>
        <?php endif; ?>

        <p class="text-center">
                <a href="<?= e(url('edit_recipe.php?id=' . $recipe->id)) ?>" class="inline-block rounded-2xl border-2 border-emerald-600 px-6 md:px-8 py-2 text-lg md:text-xl font-bold hover:bg-emerald-600 hover:text-white">Modifier</a>
        </p>
</article>
