<?php
namespace core\base;

use core\base\interfaces\IMiddlewareBase;
use core\server\Request;
use core\server\Response;

class MiddlewareBase implements IMiddlewareBase {
    function handle(Request &$request, Response &$response): bool { return true; }
    function run(Request &$request, Response &$response):bool { return $this->handle($request, $response); }
}