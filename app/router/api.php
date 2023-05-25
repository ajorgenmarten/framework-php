<?php

use app\controllers\HomeController;
use app\middlewares\Check;
use core\server\Router;

$router = new Router();

$router->get('/api', [HomeController::class, "test_middleware"]);
$router->get('/api/user/:id', HomeController::class);
$router->get('/api/image', [HomeController::class, "respond_image"]);

return $router;