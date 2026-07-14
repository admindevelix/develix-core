<?php

use Core\Router;
use App\Controllers\DashboardController;

$router = new Router();

$router->get('/dashboard', [DashboardController::class, 'index']);

return $router;