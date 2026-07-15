<?php

namespace Core\View;

class Component
{
    public static function render(string $component, array $data = []): void
    {
        extract($data);

        $basePath = defined('BASE_PATH')
            ? BASE_PATH
            : dirname(__DIR__, 4);

        $componentPath = $basePath . "/app/Components/{$component}.php";

        if (!file_exists($componentPath)) {
            throw new \RuntimeException(
                "Componente não encontrado: {$component}"
            );
        }

        require $componentPath;
    }
}