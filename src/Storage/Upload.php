<?php

namespace Core\Storage;

use RuntimeException;

class Upload
{
    public static function save(
        array $file,
        string $destination,
        array $allowedExtensions = []
    ): string {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Falha no envio do arquivo.');
        }

        $originalName = $file['name'] ?? '';
        $temporaryPath = $file['tmp_name'] ?? '';
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (
            !empty($allowedExtensions)
            && !in_array($extension, $allowedExtensions, true)
        ) {
            throw new RuntimeException('Tipo de arquivo não permitido.');
        }

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $fileName = bin2hex(random_bytes(16));

        if ($extension !== '') {
            $fileName .= '.' . $extension;
        }

        $finalPath = rtrim($destination, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . $fileName;

        if (!move_uploaded_file($temporaryPath, $finalPath)) {
            throw new RuntimeException('Não foi possível salvar o arquivo.');
        }

        return $fileName;
    }
}