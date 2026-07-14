<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

$router = require dirname(__DIR__) . '/config/routes.php';

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($uri, $method);