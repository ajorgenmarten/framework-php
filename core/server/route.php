<?php
namespace core\server;

use core\server\interfaces\IRoute;

class Route implements IRoute {
    public array $route_params = [];
    public ?string $path = null;
    public $controller = null;
    public ?string $method = null;
    private array $middlewares = [];
    
    function __construct(string $path, string|callable $controller, string $method)
    {
        $this->path = $path;
        $this->controller = $controller;
        $this->method = $method;
        $this->get_route_params($path);
    }
    function middlewares(array|string $middleware)
    {
        if(is_string($middleware)) $this->middlewares[] = $middleware;
        else $this->middlewares = $middleware;
        return $this;
    }
    
    //GET NAMES OF DYNAMIC PARAMS FOR THE URL OF THE THIS ROUTE
    private function get_route_params(string $path):void {
        $match = preg_match_all("/:(\w+)\??/", $path, $matches);
        if(!$match) return;
        foreach ($matches[1] as $value) array_push($this->route_params, $value);
    }
    //WHEN RUTE IS FOUND, THIS METHOD INIT LOGIC IN THE CONTROLLER SET
    public function run(Request $request, Response $response) {
        if(!$this->run_middlewares($request, $response)) return; 
        if(is_callable($this->controller)) return call_user_func($this->controller, $request, $response);
        else return call_user_func_array([new $this->controller, "_call"], [$this->method, [$request, $response]]);
    }
    private function run_middlewares(Request &$request, Response &$response):bool {
        foreach($this->middlewares as $middelware) {
            $next = new $middelware();
            if(!$next->run($request, $response)) return false;
        }
        return true;
    }
}