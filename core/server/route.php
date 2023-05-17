<?php
namespace core\server;

use core\server\interfaces\IRoute;

class Route implements IRoute {
    public array $route_params = [];
    public ?string $path = null;
    public $controller = null;
    public ?string $method = null;
    private array $middlewares = [];
    private ?string $name = null;
    function __construct(string $path, string|callable $controller, string $method)
    {
        $this->path = $path;
        $this->controller = $controller;
        $this->method = $method;
        $this->get_route_params($path);
    }
    function middlewares(array|string $middleware)
    {
        return $this;
    }
    function name(string $name):Route {
        $this->name = $name;
        return $this;
    }
    //GET NAMES OF DYNAMIC PARAMS FOR THE URL OF THE THIS ROUTE
    private function get_route_params(string $path):void {
        $match = preg_match_all("/:(\w+)\??/", $path, $matches);
        if(!$match) return;
        foreach ($matches[1] as $value) array_push($this->route_params, $value);
    }
    public function run(Request $request, Response $response) {
        if(is_callable($this->controller)) return call_user_func($this->controller, $request, $response);
        else return call_user_func_array([new $this->controller, $this->method], [$request, $response]);
    }
}