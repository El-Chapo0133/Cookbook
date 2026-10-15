<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$existing = $id > 0 ? recipes()->findById($id) : null;

if ($existing === null) {
    http_response_code(404);
    render('not_found', ['title' => 'Recette introuvable']);
    exit;
}

$error = null;
$recipe = $existing;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $uploader = image_uploader();
    $newImage = null;

    try {
        $recipe = Recipe::fromPost($_POST);
        $recipe->id = $existing->id;
        $newImage = $uploader->saveRecipeImage($_FILES['RecipeImage'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
        $recipe->imagePath = $newImage ?? $existing->imagePath;
        recipes()->update($recipe);
        if ($newImage !== null && $existing->imagePath !== null) {
            $uploader->delete($existing->imagePath);
        }
        redirect('recipe.php?id=' . $recipe->id);
    } catch (InvalidArgumentException $e) {
        $error = $e->getMessage();
        if ($newImage !== null) {
            $uploader->delete($newImage);
        }
        $recipe->id = $existing->id;
        $recipe->imagePath = $existing->imagePath;
    } catch (Throwable) {
        $error = 'Impossible d’enregistrer la recette.';
        if ($newImage !== null) {
            $uploader->delete($newImage);
        }
        $recipe->id = $existing->id;
        $recipe->imagePath = $existing->imagePath;
    }
}

render('recipe_form', [
    'title' => 'Modifier une recette',
    'styles' => ['assets/css/form.css'],
    'scripts' => ['assets/js/add_recipe.js'],
    'error' => $error,
    'recipe' => $recipe,
    'action' => url('edit_recipe.php?id=' . $existing->id),
    'submitLabel' => 'Enregistrer',
]);
