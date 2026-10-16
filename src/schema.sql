-- Schéma du livret de recettes (SQLite).
-- Les étapes sont stockées dans recipes.steps, séparées par « § ».
-- L'image est un fichier sur disque ; recipes.image_path en garde le chemin relatif.

CREATE TABLE IF NOT EXISTS units (
        id INTEGER PRIMARY KEY,
        code TEXT NOT NULL UNIQUE,
        label TEXT NOT NULL
);

INSERT OR IGNORE INTO units (id, code, label) VALUES
        (1, 'g', 'Gramme'),
        (2, 'kg', 'Kilogramme'),
        (3, 'ml', 'Millilitre'),
        (4, 'dl', 'Décilitre'),
        (5, 'l', 'Litre'),
        (6, 'portion', 'Portion');

CREATE TABLE IF NOT EXISTS recipes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL CHECK (length(title) > 0),
        people INTEGER NOT NULL DEFAULT 0 CHECK (people >= 0),
        steps TEXT NOT NULL DEFAULT '',
        image_path TEXT,
        duration INTEGER NOT NULL DEFAULT 0 CHECK (duration >= 0),
        note TEXT NOT NULL DEFAULT '',
        created_at TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
        modified_at TEXT NOT NULL DEFAULT (datetime('now', 'localtime'))
);

CREATE TABLE IF NOT EXISTS ingredients (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        recipe_id INTEGER NOT NULL REFERENCES recipes(id) ON DELETE CASCADE,
        name TEXT NOT NULL CHECK (length(name) > 0),
        quantity REAL NOT NULL CHECK (quantity >= 0),
        unit_id INTEGER NOT NULL REFERENCES units(id),
        position INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS idx_ingredients_recipe ON ingredients(recipe_id);

CREATE TABLE IF NOT EXISTS tags (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL UNIQUE COLLATE NOCASE
                CHECK (length(name) > 0)
);

CREATE TABLE IF NOT EXISTS recipe_tags (
        recipe_id INTEGER NOT NULL REFERENCES recipes(id) ON DELETE CASCADE,
        tag_id INTEGER NOT NULL REFERENCES tags(id) ON DELETE CASCADE,
        PRIMARY KEY (recipe_id, tag_id)
);

CREATE INDEX IF NOT EXISTS idx_recipe_tags_tag ON recipe_tags(tag_id);
