<?php
namespace app\middlewares;

use core\base\MiddlewareBase;
use core\server\Request;
use core\server\Response;

class Check extends MiddlewareBase {
    function handle(Request &$request, Response &$response): bool
    {
        return true;
    }
}