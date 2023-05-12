<?php
namespace core\server;

class Query {
    function __get($name) { return $_GET[$name] ?? null; }
    function __set($name, $value) { $_GET[$name] = $value; }
    function __unset($name) { unset($_GET[$name]); }
}