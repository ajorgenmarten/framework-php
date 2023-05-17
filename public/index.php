<?php
chdir( dirname(__DIR__) );

require_once "./core/autoload.php";

use app\controllers\HomeController;
use core\Autoload;
use core\server\Request;
use core\server\Router;

new Autoload();

$router = new Router();
$router->get("/users", function($request, $response) {
    print_r($request);
});
$router->get("/users/:email/:number?", HomeController::class)->name("number.email");
$router->boot();