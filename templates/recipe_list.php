<?php
/** @var list<Recipe> $recipes */
$emptyMessage = $emptyMessage ?? 'Aucune recette.';
?>
<?php if ($recipes === []): ?>
<p class="text-center text-stone-600 py-6"><?= e($emptyMessage) ?></p>
<?php else: ?>
<table class="w-full border-collapse">
        <thead>
                <tr class="border-b-2 border-emerald-600 text-left">
                        <th class="p-2">Titre</th>
                        <th class="p-2">Durée</th>
                        <th class="p-2">Ajoutée le</th>
                        <th class="p-2">Modifiée le</th>
                </tr>
        </thead>
        <tbody>
                <?php foreach ($recipes as $item): ?>
                <tr class="border-b border-stone-200 hover:bg-emerald-50">
                        <td class="p-2"><a class="block text-emerald-800 hover:underline font-medium" href="<?= e(url('recipe.php?id=' . $item->id)) ?>"><?= e($item->title) ?></a></td>
                        <td class="p-2"><?= $item->duration > 0 ? e((string) $item->duration) . ' min' : '—' ?></td>
                        <td class="p-2 whitespace-nowrap"><?= e($item->createdAt !== '' ? $item->createdAt : '—') ?></td>
                        <td class="p-2 whitespace-nowrap"><?= e($item->modifiedAt !== '' ? $item->modifiedAt : '—') ?></td>
                </tr>
                <?php endforeach; ?>
        </tbody>
</table>
<?php endif; ?>
