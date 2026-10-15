<?php

declare(strict_types=1);

final class RecipeRepository
{
    private const ORDER_SQL = [
        'modified_at' => 'r.modified_at DESC, r.id DESC',
        'duration' => 'r.duration ASC, r.title COLLATE NOCASE',
        'created_at' => 'r.created_at DESC, r.id DESC',
    ];

    private const SEARCH_SQL = [
        'ingredients' => 'SELECT DISTINCT r.*
            FROM recipes r
            INNER JOIN ingredients i ON i.recipe_id = r.id
            WHERE i.name LIKE :q ESCAPE \'\\\'
            ORDER BY %s',
        'tag' => 'SELECT DISTINCT r.*
            FROM recipes r
            INNER JOIN recipe_tags rt ON rt.recipe_id = r.id
            INNER JOIN tags t ON t.id = rt.tag_id
            WHERE t.name LIKE :q ESCAPE \'\\\'
            ORDER BY %s',
        'name' => 'SELECT r.*
            FROM recipes r
            WHERE r.title LIKE :q ESCAPE \'\\\'
            ORDER BY %s',
    ];

    public function __construct(private PDO $pdo)
    {
    }

    public function count(): int
    {
        return (int) $this->run('SELECT COUNT(*) FROM recipes')->fetchColumn();
    }

    /** @return list<Recipe> */
    public function findAll(): array
    {
        return $this->hydrateAll(
            $this->run('SELECT * FROM recipes ORDER BY title COLLATE NOCASE')->fetchAll()
        );
    }

    /** @return list<Recipe> */
    public function findLatest(int $limit = 4): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM recipes ORDER BY created_at DESC, id DESC LIMIT :limit'
        );
        $stmt->bindValue(':limit', max(1, min(100, $limit)), PDO::PARAM_INT);
        $stmt->execute();

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function findById(int $id): ?Recipe
    {
        $row = $this->run('SELECT * FROM recipes WHERE id = :id', ['id' => $id])->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findRandomId(): ?int
    {
        $id = $this->run('SELECT id FROM recipes ORDER BY RANDOM() LIMIT 1')->fetchColumn();

        return $id === false ? null : (int) $id;
    }

    /**
     * @param 'name'|'ingredients'|'tag' $field
     * @param 'created_at'|'modified_at'|'duration' $order
     * @return list<Recipe>
     */
    public function search(string $query, string $field, string $order): array
    {
        $orderSql = self::ORDER_SQL[$order] ?? self::ORDER_SQL['created_at'];
        $query = trim($query);

        if ($query === '') {
            $sql = 'SELECT r.* FROM recipes r ORDER BY ' . $orderSql;
            $rows = $this->run($sql)->fetchAll();
        } else {
            $template = self::SEARCH_SQL[$field] ?? self::SEARCH_SQL['name'];
            $sql = sprintf($template, $orderSql);
            $rows = $this->run($sql, ['q' => like_pattern($query)])->fetchAll();
        }

        return $this->hydrateAll($rows);
    }

    /** @return list<Recipe> */
    public function findByTag(string $tag): array
    {
        return $this->search($tag, 'tag', 'created_at');
    }

    public function create(Recipe $recipe): Recipe
    {
        $this->pdo->beginTransaction();

        try {
            $this->run(
                'INSERT INTO recipes (title, people, steps, image_path, duration, note)
                 VALUES (:title, :people, :steps, :image_path, :duration, :note)',
                [
                    'title' => $recipe->title,
                    'people' => $recipe->people,
                    'steps' => $recipe->stepsStorage(),
                    'image_path' => $recipe->imagePath,
                    'duration' => $recipe->duration,
                    'note' => $recipe->note,
                ]
            );

            $id = (int) $this->pdo->lastInsertId();
            $this->saveIngredients($id, $recipe->ingredients);
            $this->saveTags($id, $recipe->tags);
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        $recipe->id = $id;

        return $recipe;
    }

    public function update(Recipe $recipe): void
    {
        if ($recipe->id === null) {
            throw new InvalidArgumentException('Recette sans identifiant.');
        }

        $this->pdo->beginTransaction();

        try {
            $this->run(
                'UPDATE recipes
                 SET title = :title,
                     people = :people,
                     steps = :steps,
                     image_path = :image_path,
                     duration = :duration,
                     note = :note,
                     modified_at = datetime(\'now\', \'localtime\')
                 WHERE id = :id',
                [
                    'id' => $recipe->id,
                    'title' => $recipe->title,
                    'people' => $recipe->people,
                    'steps' => $recipe->stepsStorage(),
                    'image_path' => $recipe->imagePath,
                    'duration' => $recipe->duration,
                    'note' => $recipe->note,
                ]
            );

            $this->run('DELETE FROM ingredients WHERE recipe_id = :id', ['id' => $recipe->id]);
            $this->run('DELETE FROM recipe_tags WHERE recipe_id = :id', ['id' => $recipe->id]);
            $this->saveIngredients($recipe->id, $recipe->ingredients);
            $this->saveTags($recipe->id, $recipe->tags);
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function delete(int $id, ?ImageUploader $uploader = null): void
    {
        $recipe = $this->findById($id);
        if ($recipe === null) {
            return;
        }

        $this->run('DELETE FROM recipes WHERE id = :id', ['id' => $id]);

        if ($uploader !== null) {
            $uploader->delete($recipe->imagePath);
        }
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): Recipe
    {
        $id = (int) $row['id'];

        $ingredients = [];
        foreach ($this->run(
            'SELECT name, quantity, unit_id
             FROM ingredients
             WHERE recipe_id = :id
             ORDER BY position, id',
            ['id' => $id]
        )->fetchAll() as $ingredient) {
            $ingredients[] = [
                'name' => (string) $ingredient['name'],
                'quantity' => (float) $ingredient['quantity'],
                'unit_id' => (int) $ingredient['unit_id'],
            ];
        }

        $tags = array_map(
            static fn (array $t): string => (string) $t['name'],
            $this->run(
                'SELECT t.name
                 FROM tags t
                 INNER JOIN recipe_tags rt ON rt.tag_id = t.id
                 WHERE rt.recipe_id = :id
                 ORDER BY t.name COLLATE NOCASE',
                ['id' => $id]
            )->fetchAll()
        );

        return new Recipe(
            title: (string) $row['title'],
            people: (int) $row['people'],
            tags: $tags,
            ingredients: $ingredients,
            steps: Recipe::stepsFromStorage((string) $row['steps']),
            duration: (int) $row['duration'],
            note: (string) $row['note'],
            imagePath: $row['image_path'] !== null ? (string) $row['image_path'] : null,
            id: $id,
            createdAt: (string) ($row['created_at'] ?? ''),
            modifiedAt: (string) ($row['modified_at'] ?? ''),
        );
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<Recipe>
     */
    private function hydrateAll(array $rows): array
    {
        $recipes = [];
        foreach ($rows as $row) {
            $recipes[] = $this->hydrate($row);
        }

        return $recipes;
    }

    /**
     * @param list<array{name: string, quantity: float, unit_id: int}> $ingredients
     */
    private function saveIngredients(int $recipeId, array $ingredients): void
    {
        $sql = 'INSERT INTO ingredients (recipe_id, name, quantity, unit_id, position)
                VALUES (:recipe_id, :name, :quantity, :unit_id, :position)';

        foreach ($ingredients as $position => $ingredient) {
            $this->run($sql, [
                'recipe_id' => $recipeId,
                'name' => $ingredient['name'],
                'quantity' => $ingredient['quantity'],
                'unit_id' => $ingredient['unit_id'],
                'position' => $position,
            ]);
        }
    }

    /** @param list<string> $tags */
    private function saveTags(int $recipeId, array $tags): void
    {
        foreach ($tags as $name) {
            $this->run('INSERT OR IGNORE INTO tags (name) VALUES (:name)', ['name' => $name]);
            $tagId = (int) $this->run(
                'SELECT id FROM tags WHERE name = :name COLLATE NOCASE',
                ['name' => $name]
            )->fetchColumn();
            $this->run(
                'INSERT OR IGNORE INTO recipe_tags (recipe_id, tag_id) VALUES (:recipe_id, :tag_id)',
                [
                    'recipe_id' => $recipeId,
                    'tag_id' => $tagId,
                ]
            );
        }
    }

    /**
     * Every query with values goes through PDO::prepare.
     *
     * @param array<string, float|int|string|null> $params
     */
    private function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $name => $value) {
            $placeholder = str_starts_with((string) $name, ':') ? (string) $name : ':' . $name;

            if (is_int($value)) {
                $stmt->bindValue($placeholder, $value, PDO::PARAM_INT);
            } elseif ($value === null) {
                $stmt->bindValue($placeholder, null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue($placeholder, $value, PDO::PARAM_STR);
            }
        }

        $stmt->execute();

        return $stmt;
    }
}
