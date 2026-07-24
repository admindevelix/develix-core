<?php

declare(strict_types=1);

namespace Core\View;

final class View
{
    private static ?string $basePath = null;

    public static function setBasePath(string $basePath): void
    {
        self::$basePath = rtrim($basePath, '/\\');
    }

    public static function render(
        string $view,
        array $data = [],
        ?string $layout = 'app'
    ): void {
        $basePath = self::$basePath
            ?? (defined('BASE_PATH')
                ? BASE_PATH
                : dirname(__DIR__, 2));

        $viewPath = $basePath . "/app/Views/{$view}.php";

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View não encontrada: {$view}");
        }

        extract($data);

        if ($layout === null) {
            require $viewPath;

            return;
        }

        $layoutPath = $basePath . "/app/Layouts/{$layout}.php";

        if (!file_exists($layoutPath)) {
            throw new \RuntimeException("Layout não encontrado: {$layout}");
        }

        require $layoutPath;
    }
}