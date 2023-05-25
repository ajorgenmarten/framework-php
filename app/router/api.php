<?php

use app\controllers\HomeController;
use core\server\Router;

$router = new Router();

$router->get('/', HomeController::class);

return $router;