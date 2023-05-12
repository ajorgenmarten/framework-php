<?php
chdir( dirname(__DIR__) );

require_once "./core/autoload.php";

use core\Autoload;
use core\server\Request;
use core\server\Router;

new Autoload();

$router = new Router();
$router->get("/users", "controller");
$router->get("/users/:email/:number?", "controller")->name("number.email");
$router->boot();

$request = new Request();

print_r($request);