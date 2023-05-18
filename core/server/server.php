<?php
namespace core\server;

class Server {
    function init() {
        $routes = scandir('./app/router');
        array_shift($routes);
        array_shift($routes);
        foreach ($routes as $file) {
            $router = require_once "./app/router/$file";
            if(is_object($router)) $router->boot();
        }
        header("HTTP/1.0 404 Not Found", true, 404);
    }
}