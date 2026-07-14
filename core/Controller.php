<?php

namespace Core;

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);

        $viewPath = dirname(__DIR__) . "/app/Views/{$view}.php";

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View não encontrada: {$view}");
        }

        require dirname(__DIR__) . "/app/Layouts/header.php";
        require dirname(__DIR__) . "/app/Layouts/sidebar.php";
        require dirname(__DIR__) . "/app/Layouts/topbar.php";
        require $viewPath;
        require dirname(__DIR__) . "/app/Layouts/footer.php";
    }

    protected function component(string $component, array $data = []): void
    {
        extract($data);

        $componentPath = dirname(__DIR__) . "/app/Components/{$component}.php";

        if (!file_exists($componentPath)) {
            throw new \RuntimeException("Componente não encontrado: {$component}");
        }

        require $componentPath;
    }
}