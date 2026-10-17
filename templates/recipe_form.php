<?php
/** @var ?Recipe $recipe */
$recipe = $recipe ?? null;
$action = $action ?? url('add_recipe.php');
$submitLabel = $submitLabel ?? 'Enregistrer';
$ingredients = $recipe !== null && $recipe->ingredients !== [] ? $recipe->ingredients : [['name' => '', 'quantity' => null, 'unit_id' => Unit::G]];
$steps = $recipe !== null && $recipe->steps !== [] ? $recipe->steps : [''];
$tags = $recipe?->tags ?? [];
?>
<form id="recipeForm" class="flex flex-col gap-4 w-8/10" method="post" action="<?= e($action) ?>" enctype="multipart/form-data">
        <?php if ($recipe?->id): ?>
        <input type="hidden" name="id" value="<?= (int) $recipe->id ?>">
        <?php endif; ?>
        <?php if (!empty($error)): ?>
        <p class="text-center text-red-700"><?= e($error) ?></p>
        <?php endif; ?>
        <div class="flex flex-col items-center justify-center gap-4">
                <input type="text" class="text-4xl border-b-2 min-w-120 w-1/3 text-center" maxlength="255" name="RecipeName" placeholder="Donnez un nom à la recette !" required value="<?= e($recipe?->title ?? '') ?>">
                <?php if ($recipe?->imagePath): ?>
                <img src="<?= e(url($recipe->imagePath)) ?>" alt="" class="max-h-40 object-contain">
                <?php endif; ?>
                <div class="text-stone-600 flex flex-col items-center gap-2 w-full max-w-md">
                        Image
                        <div class="file-box-row">
                                <input type="file" name="RecipeImage" accept="image/jpeg,image/png,image/webp" class="file-box-input">
                                <?php if ($recipe?->id && $recipe->imagePath): ?>
                                <button type="submit" name="delete_image" value="1" class="file-box-delete" title="Supprimer l’image">Supprimer</button>
                                <?php endif; ?>
                        </div>
                </div>
        </div>
        <div class="flex w-full gap-4">
                <div class="p-4 flex w-2/3 gap-4 flex-col items-center">
                        <p class="text-xl">Ingrédients</p>
                        <div id="recipeIngredients" class="w-full flex flex-col gap-2">
                                <?php foreach ($ingredients as $index => $ingredient): ?>
                                <?php $n = $index + 1; ?>
                                <div>
                                        <input type="text" maxlength="255" placeholder="Nom de l'ingrédient" name="RecipeIngrName<?= $n ?>" class="border w-5/8" value="<?= e($ingredient['name'] ?? '') ?>">
                                        <input type="number" min="0" max="10000" step="any" placeholder="Quantité" name="RecipeIngrVal<?= $n ?>" class="border w-1/8" value="<?= isset($ingredient['quantity']) ? e((string) $ingredient['quantity']) : '' ?>">
                                        <select name="RecipeIngrUnit<?= $n ?>" class="border w-1/8">
                                                <?php foreach (Unit::CODES as $unitId => $code): ?>
                                                <option value="<?= $unitId ?>"<?= (int) ($ingredient['unit_id'] ?? Unit::G) === $unitId ? ' selected' : '' ?>><?= e($code) ?></option>
                                                <?php endforeach; ?>
                                        </select>
                                </div>
                                <?php endforeach; ?>
                        </div>
                        <button type="button" onclick="add_recipe_ingredient()">
                                <img src="<?= e(url('assets/images/add.png')) ?>" alt="Ajouter un ingrédient" class="w-10 mt-4 cursor-pointer">
                        </button>
                </div>
                <div class="flex w-1/3 flex-col gap-10 p-8">
                        <div class="flex flex-col items-center">
                                <p class="text-xl">Combien de personnes ?</p>
                                <p class="text-stone-500">Laissez 0 si pas de réponse</p>
                                <input type="number" name="RecipePeopleAmount" value="<?= e((string) ($recipe?->people ?? 0)) ?>" min="0" max="20" class="text-center border">
                        </div>
                        <div class="flex flex-col items-center">
                                <p class="text-xl">Temps (minutes)</p>
                                <input type="number" name="RecipeDuration" value="<?= e((string) ($recipe?->duration ?? 0)) ?>" min="0" max="10000" class="text-center border">
                        </div>
                        <div class="flex flex-col items-center">
                                <p class="text-xl">Étiquettes</p>
                                <p class="text-stone-500">Espace, Entrée ou virgule pour valider</p>
                                <input type="text" id="fakeInputForTags" name="RecipeTagDraft" class="text-center border" maxlength="64" autocomplete="off">
                                <input type="hidden" id="inputForRecipeTags" name="RecipeTags" value="<?= e(implode(';', $tags)) ?>">
                                <div id="divForRecipeTags" class="w-full mt-4 flex flex-wrap justify-center gap-4">
                                        <?php foreach ($tags as $tag): ?>
                                        <div id="tag<?= e($tag) ?>" class="border rounded-xl px-3 flex items-center gap-1">
                                                <p><?= e($tag) ?></p>
                                                <button type="button" onclick="remove_tag('<?= e($tag) ?>')">
                                                        <img src="<?= e(url('assets/images/cross.png')) ?>" alt="Retirer" class="w-4 h-4 cursor-pointer">
                                                </button>
                                        </div>
                                        <?php endforeach; ?>
                                </div>
                        </div>
                </div>
        </div>
        <div class="flex items-center flex-col gap-2 p-4">
                <p class="text-xl">Marche à suivre</p>
                <div id="recipeSteps" class="flex flex-col w-full gap-2">
                        <?php foreach ($steps as $index => $step): ?>
                        <div class="flex w-full items-center">
                                <p class="text-l w-20">Étape <?= $index + 1 ?></p>
                                <textarea class="border rounded-xl w-full h-auto text-clip p-2" name="RecipeText<?= $index + 1 ?>"><?= e($step) ?></textarea>
                        </div>
                        <?php endforeach; ?>
                </div>
                <button type="button" onclick="add_recipe_text()">
                        <img src="<?= e(url('assets/images/add.png')) ?>" alt="Ajouter une étape" class="w-10 mt-4 cursor-pointer">
                </button>
        </div>
        <div class="flex flex-col items-center gap-2 p-4">
                <p class="text-xl">Note</p>
                <p class="text-stone-500">Rappel personnel, visible sur la recette</p>
                <textarea class="border rounded-xl w-full h-auto text-clip p-2" name="RecipeNote"><?= e($recipe?->note ?? '') ?></textarea>
        </div>
        <div class="flex flex-col items-center gap-4 mx-60 mb-8">
                <input type="submit" value="<?= e($submitLabel) ?>" class="w-full rounded-2xl border-2 border-emerald-600 py-2 min-w-0 text-xl font-bold hover:bg-emerald-600 hover:text-white cursor-pointer">
                <?php if ($recipe?->id): ?>
                <button type="submit" name="delete" value="1" class="w-1/2 rounded-2xl bg-red-600 text-white py-2 text-xl font-bold hover:bg-red-700 cursor-pointer" onclick="return confirm('Supprimer cette recette ?');">Supprimer</button>
                <?php endif; ?>
        </div>
</form>
