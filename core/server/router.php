<?php
namespace core\server;

use core\server\interfaces\IRouter;
use core\server\Route;
use core\server\Response;
use Exception;

class Router implements IRouter {
    private array $routes = [
        "GET" => [],
        "POST" => [],
        "PUT" => [],
        "DELETE" => []
    ];

    private function get_controller_data(array|string $controller): array
    {
        $ctrl = null;
        $mthd = null;
        if(is_string($controller)) $ctrl = $controller;
        if(is_array($controller)) {
            if(count($controller) == 2) {$ctrl = $controller[0]; $mthd = $controller[1];}
            if(count($controller) == 1) $ctrl = $controller[0];
            if(!$controller) throw new Exception("Expected 1 or 2 items in the controller array",1);
        }
        return compact("ctrl", "mthd");
    }
    private function save_route(string $method, string $path, string|array $controller): Route
    {
        extract($this->get_controller_data($controller));
        $route = new Route($path, $ctrl, $mthd ?? "index");
        array_push($this->routes[$method], $route);
        return $route;
    }

    function get(string $path, array|string $controller):Route { return $this->save_route("GET", $path, $controller); }
    function post(string $path, array|string $controller):Route { return $this->save_route("POST", $path, $controller); }
    function put(string $path, array|string $controller):Route { return $this->save_route("PUT", $path, $controller); }
    function delete(string $path, array|string $controller):Route { return $this->save_route("DELETE", $path, $controller); }

    private function parse_path_to_regexp(string $path) {
        $path_array = preg_split("/\//", $path, -1, PREG_SPLIT_NO_EMPTY);
        $path_array = array_map(function($section) {
            $section = trim($section);
            $param_pattern = "/:[a-zA-Z_]+(\?)*/";
            if(preg_match($param_pattern, $section, $matches) == false) return $section;
            $section = ($matches[1] ?? false) ? "[^\/]*" : "[^\/]+";
            return $section;
        },$path_array);
        
        $path = implode("\/", $path_array);

        return "/^\/$path"."[\/]{0,1}$/";
    }
    

    function boot():void {
        $request_method = Http::get_request_method();
        $request_path = Http::sanitize_url ( Http::get_path() );

        foreach ($this->routes[$request_method] ?? [] as $route) {
            $reg_exp = $this->parse_path_to_regexp($route->path);
            $match_route = preg_match($reg_exp, $request_path, $matches);
            if(!$match_route) continue;
            $request = new Request();
            $request->route_path = $route->path;
            $response = new Response();
            $route->run($request, $response);
            die;
            // var_dump($this->get_route_params($request_path,$value->path));
            // print_r($this->get_route_params($request_path,$value->path));
            // print_r($matches);
            // print_r($matc_route);
        }
    }
}