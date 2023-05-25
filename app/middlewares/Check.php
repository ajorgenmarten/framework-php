<?php
namespace app\middlewares;

use core\base\MiddlewareBase;
use core\server\Request;
use core\server\Response;

class Check extends MiddlewareBase {
    function handle(Request &$request, Response &$response): bool
    {
        echo "pase por middleware check \n";
        $request->body->name = "Pepe";
        $request->body->lastname = "Antonio";
        return true;
    }
}