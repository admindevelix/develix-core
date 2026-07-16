<?php

namespace Core;

class Router
{
    private array $routes = [];

    public function get(string $uri, array $action): void
    {
        $this->routes['GET'][$this->normalize($uri)] = $action;
    }

    public function post(string $uri, array $action): void
    {
        $this->routes['POST'][$this->normalize($uri)] = $action;
    }

    public function dispatch(): void
    {
        $uri = \Core\Http\Request::uri();

        $method = \Core\Http\Request::method();

        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);
            exit('404 - Página não encontrada');
        }

        [$controller, $action] = $this->routes[$method][$uri];

        $controller = new $controller();

        $controller->$action();
    }

    private function normalize(string $uri): string
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        return '/' . trim($uri, '/');
    }
}