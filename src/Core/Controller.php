<?php

namespace Core;

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);

        $basePath = defined('BASE_PATH')
            ? BASE_PATH
            : dirname(__DIR__, 3);

        $viewPath = $basePath . "/app/Views/{$view}.php";

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View não encontrada: {$view}");
        }

        require $basePath . "/app/Layouts/header.php";
        require $basePath . "/app/Layouts/sidebar.php";
        require $basePath . "/app/Layouts/topbar.php";
        require $viewPath;
        require $basePath . "/app/Layouts/footer.php";
    }

    protected function component(string $component, array $data = []): void
    {
        extract($data);

        $basePath = defined('BASE_PATH')
            ? BASE_PATH
            : dirname(__DIR__, 3);

        $componentPath = $basePath . "/app/Components/{$component}.php";

        if (!file_exists($componentPath)) {
            throw new \RuntimeException(
                "Componente não encontrado: {$component}"
            );
        }

        require $componentPath;
    }
}