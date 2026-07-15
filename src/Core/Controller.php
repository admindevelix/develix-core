<?php

namespace Core;

use Core\View\View;

class Controller
{
    protected function view(
        string $view,
        array $data = [],
        ?string $layout = 'app'
    ): void {
        View::render($view, $data, $layout);
    }

    protected function component(string $component, array $data = []): void
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