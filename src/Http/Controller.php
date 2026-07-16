<?php

namespace Core\Http;

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
}