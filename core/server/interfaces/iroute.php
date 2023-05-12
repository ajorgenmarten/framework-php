<?php
namespace core\server\interfaces;

use core\server\Request;
use core\server\Response;

interface IRoute {
    function middlewares(array|string $middleware);
    function run(Request $request, Response $response);
}