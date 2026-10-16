<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

$error = null;
$recipe = null;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $uploader = image_uploader();

    if (post_too_large()) {
        $error = 'L’envoi est trop volumineux (8 Mo maximum pour l’image).';
    } else {
        try {
            $recipe = Recipe::fromPost($_POST);
            $recipe->imagePath = $uploader->saveRecipeImage($_FILES['RecipeImage'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
            $recipe = recipes()->create($recipe);
            redirect('recipe.php?id=' . $recipe->id);
        } catch (InvalidArgumentException | RuntimeException $e) {
            $error = $e->getMessage();
            if ($recipe?->imagePath) {
                $uploader->delete($recipe->imagePath);
            }
        } catch (Throwable) {
            $error = 'Impossible d’enregistrer la recette.';
            if ($recipe?->imagePath) {
                $uploader->delete($recipe->imagePath);
            }
        }
    }
}

render('recipe_form', [
    'title' => 'Ajouter une recette',
    'styles' => ['assets/css/form.css'],
    'scripts' => ['assets/js/add_recipe.js'],
    'error' => $error,
    'recipe' => $recipe,
    'action' => url('add_recipe.php'),
    'submitLabel' => 'Enregistrer',
]);
