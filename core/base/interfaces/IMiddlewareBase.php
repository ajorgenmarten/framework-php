<?php
namespace core\base\interfaces;

use core\server\Request;
use core\server\Response;

interface IMiddlewareBase {
    function handle(Request &$request, Response &$response):bool;
    function run (Request &$request, Response &$response):bool;
}