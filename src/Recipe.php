<?php

declare(strict_types=1);

/**
 * @phpstan-type Ingredient array{name: string, quantity: float, unit_id: int}
 */
final class Recipe
{
    public const STEP_SEPARATOR = '§';
    public const MAX_INGREDIENT_COUNT = 100;
    public const MAX_STEP_COUNT = 100;

    /**
     * @param list<string> $tags
     * @param list<Ingredient> $ingredients
     * @param list<string> $steps
     */
    public function __construct(
        public string $title,
        public int $people,
        public array $tags,
        public array $ingredients,
        public array $steps,
        public int $duration,
        public string $note,
        public ?string $imagePath = null,
        public ?int $id = null,
        public string $createdAt = '',
        public string $modifiedAt = '',
    ) {
    }

    public function stepsStorage(): string
    {
        return implode(self::STEP_SEPARATOR, $this->steps);
    }

    /** @return list<string> */
    public static function stepsFromStorage(string $stored): array
    {
        if ($stored === '') {
            return [];
        }

        return explode(self::STEP_SEPARATOR, $stored);
    }

    /**
     * @param array<string, mixed> $post
     */
    public static function fromPost(array $post): self
    {
        $title = trim((string) ($post['RecipeName'] ?? ''));
        if ($title === '' || char_len($title) > 255) {
            throw new InvalidArgumentException('Le titre est obligatoire (255 caractères maximum).');
        }

        $people = (int) ($post['RecipePeopleAmount'] ?? 0);
        $duration = (int) ($post['RecipeDuration'] ?? 0);
        $note = trim((string) ($post['RecipeNote'] ?? ''));

        $tags = [];
        $rawTags = (string) ($post['RecipeTags'] ?? '');
        $draft = trim((string) ($post['RecipeTagDraft'] ?? ''));
        if ($draft !== '') {
            $rawTags = $rawTags === '' ? $draft : $rawTags . ';' . $draft;
        }
        foreach (preg_split('/[;,\s]+/u', $rawTags) ?: [] as $tag) {
            $tag = trim($tag);
            if ($tag === '' || char_len($tag) > 64) {
                continue;
            }
            $tags[] = $tag;
        }
        $tags = array_values(array_unique($tags));

        $ingredients = [];
        for ($i = 1; $i <= self::MAX_INGREDIENT_COUNT; $i++) {
            if (!array_key_exists('RecipeIngrName' . $i, $post)) {
                break;
            }

            $name = trim((string) $post['RecipeIngrName' . $i]);
            if ($name === '') {
                continue;
            }
            if (char_len($name) > 255) {
                throw new InvalidArgumentException('Le nom d’un ingrédient dépasse 255 caractères.');
            }

            $unitId = (int) ($post['RecipeIngrUnit' . $i] ?? Unit::G);
            if (!Unit::isValidId($unitId)) {
                $unitId = Unit::G;
            }

            $ingredients[] = [
                'name' => $name,
                'quantity' => (float) ($post['RecipeIngrVal' . $i] ?? 0),
                'unit_id' => $unitId,
            ];
        }

        $steps = [];
        for ($i = 1; $i <= self::MAX_STEP_COUNT; $i++) {
            if (!array_key_exists('RecipeText' . $i, $post)) {
                break;
            }

            $step = trim(str_replace(self::STEP_SEPARATOR, '', (string) $post['RecipeText' . $i]));
            if ($step === '') {
                continue;
            }
            $steps[] = $step;
        }

        return new self(
            title: $title,
            people: $people,
            tags: $tags,
            ingredients: $ingredients,
            steps: $steps,
            duration: $duration,
            note: $note,
        );
    }
}
