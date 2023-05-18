<?php

use app\controllers\HomeController;
use core\server\Router;

$router = new Router();

$router->get('/api', HomeController::class);
$router->get('/api/user/:id', HomeController::class);

return $router;