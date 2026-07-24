<?php

declare(strict_types=1);

namespace Core\View;

final class Component
{
    private static ?string $basePath = null;

    public static function setBasePath(string $basePath): void
    {
        self::$basePath = rtrim($basePath, '/\\');
    }

    public static function render(
        string $component,
        array $data = []
    ): void {
        $basePath = self::$basePath
            ?? (defined('BASE_PATH')
                ? BASE_PATH
                : dirname(__DIR__, 2));

        $componentPath = $basePath
            . "/app/Components/{$component}.php";

        if (!file_exists($componentPath)) {
            throw new \RuntimeException(
                "Componente não encontrado: {$component}"
            );
        }

        extract($data);

        require $componentPath;
    }
}