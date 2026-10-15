<?php

declare(strict_types=1);

final class ImageUploader
{
    private const MIMES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public function __construct(
        private string $directory,
        private string $urlPath,
        private int $maxBytes,
    ) {
    }

    /**
     * @param array<string, mixed> $file  Entrée $_FILES
     */
    public function saveRecipeImage(array $file): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Le téléversement de l’image a échoué.');
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        $size = (int) ($file['size'] ?? 0);

        if ($tmp === '' || !is_uploaded_file($tmp)) {
            throw new InvalidArgumentException('Image invalide.');
        }

        if ($size <= 0 || $size > $this->maxBytes) {
            throw new InvalidArgumentException('L’image est trop lourde (2 Mo maximum).');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($tmp);
        if (!isset(self::MIMES[$mime])) {
            throw new InvalidArgumentException('Formats acceptés : JPEG, PNG ou WebP.');
        }

        if (!is_dir($this->directory) && !mkdir($this->directory, 0755, true) && !is_dir($this->directory)) {
            throw new RuntimeException('Impossible de créer le dossier des images.');
        }

        $name = bin2hex(random_bytes(16)) . '.' . self::MIMES[$mime];
        $dest = $this->directory . '/' . $name;

        if (!move_uploaded_file($tmp, $dest)) {
            throw new RuntimeException('Impossible d’enregistrer l’image.');
        }

        return $this->urlPath . '/' . $name;
    }

    public function delete(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }

        $full = $this->directory . '/' . basename($relativePath);
        if (is_file($full)) {
            unlink($full);
        }
    }
}
