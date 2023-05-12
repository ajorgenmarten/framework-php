<?php
namespace core\server\interfaces;

use core\server\Route;

interface IRouter {
    function get(string $path, array|string $controller):Route;
    function post(string $path, array|string $controller):Route;
    function put(string $path, array|string $controller):Route;
    function delete(string $path, array|string $controller):Route;
    function boot():void;
}