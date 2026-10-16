<?php

declare(strict_types=1);

final class ImageUploader
{
    private const MIMES = [
        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/jpg' => 'jpg',
        'image/png' => 'png',
        'image/x-png' => 'png',
        'image/webp' => 'webp',
    ];

    private const IMAGE_TYPES = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_WEBP => 'webp',
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
        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE || $file['error'] === '4') {
            return null;
        }

        $error = (int) $file['error'];
        if ($error !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException($this->uploadErrorMessage($error));
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 && $tmp !== '' && is_file($tmp)) {
            $size = (int) filesize($tmp);
        }

        if ($tmp === '' || !is_file($tmp)) {
            throw new InvalidArgumentException('Image invalide.');
        }

        if ($size <= 0) {
            throw new InvalidArgumentException('Image invalide.');
        }

        if ($size > $this->maxBytes) {
            throw new InvalidArgumentException('L’image est trop lourde (' . $this->maxMega() . ' Mo maximum).');
        }

        $extension = $this->extensionForFile($tmp);
        if ($extension === null) {
            throw new InvalidArgumentException('Formats acceptés : JPEG, PNG ou WebP.');
        }

        $this->ensureDirectory();

        $name = bin2hex(random_bytes(16)) . '.' . $extension;
        $dest = $this->directory . '/' . $name;

        $moved = is_uploaded_file($tmp) ? move_uploaded_file($tmp, $dest) : false;
        if (!$moved && is_file($tmp)) {
            $moved = copy($tmp, $dest);
            if ($moved) {
                @unlink($tmp);
            }
        }

        if (!$moved || !is_file($dest)) {
            throw new RuntimeException('Impossible d’enregistrer l’image (dossier non accessible en écriture).');
        }

        @chmod($dest, 0644);

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

    private function extensionForFile(string $path): ?string
    {
        if (class_exists('finfo')) {
            $mime = (string) (new finfo(FILEINFO_MIME_TYPE))->file($path);
            if (isset(self::MIMES[$mime])) {
                return self::MIMES[$mime];
            }
        }

        $info = @getimagesize($path);
        if ($info !== false && isset(self::IMAGE_TYPES[$info[2]])) {
            return self::IMAGE_TYPES[$info[2]];
        }

        return null;
    }

    private function ensureDirectory(): void
    {
        if (!is_dir($this->directory) && !mkdir($this->directory, 0775, true) && !is_dir($this->directory)) {
            throw new RuntimeException('Impossible de créer le dossier des images.');
        }

        @chmod($this->directory, 0775);

        if (!is_writable($this->directory)) {
            throw new RuntimeException('Le dossier des images n’est pas accessible en écriture.');
        }
    }

    private function uploadErrorMessage(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'L’image est trop lourde (' . $this->maxMega() . ' Mo maximum).',
            UPLOAD_ERR_PARTIAL => 'Le téléversement de l’image a été interrompu.',
            UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant sur le serveur.',
            UPLOAD_ERR_CANT_WRITE => 'Impossible d’écrire l’image sur le disque.',
            UPLOAD_ERR_EXTENSION => 'Une extension PHP a bloqué l’image.',
            default => 'Le téléversement de l’image a échoué.',
        };
    }

    private function maxMega(): int
    {
        return max(1, (int) round($this->maxBytes / (1024 * 1024)));
    }
}
