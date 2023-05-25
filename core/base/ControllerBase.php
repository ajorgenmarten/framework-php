<?php
namespace core\base;

use core\base\abstracts\AbstractControllerBase;
use core\base\interfaces\IControllerBase;

class ControllerBase extends AbstractControllerBase implements IControllerBase {
    protected $middleware_stack = [];
    protected $middleware_apply = [];


    protected function middleware(array|string $midlleware): ControllerBase {
        $this->middleware_stack = [];
        
        if(is_string($midlleware)) $this->middleware_stack[] = $midlleware;
        else $this->middleware_stack = $midlleware;
        
        return $this;
    }

    function _call(string $name, array $arguments)
    {
        $find_method_exist = function (string $method_name, array $array_methods_middlewares):bool {
            $methods = $array_methods_middlewares["methods"];
            return in_array($method_name, $methods);
        };
        $execute_middlewares = function ($middleware_array, array &$arguments):bool {
            foreach ($middleware_array as $middleware) {
                $next = new $middleware();
                if(!$next->run($arguments[0], $arguments[1])) return false;
            }
            return true;
        };
        //run middlewares
        foreach ($this->middleware_apply as $key => $array) {
            if( $key === "all" ) {
                if(!$execute_middlewares($array["middlewares"], $arguments)) return;
            }
            if($key === "only") {
                foreach($array as $methods_middlewares) {
                    if(!$find_method_exist($name, $methods_middlewares)) continue;
                    if(!$execute_middlewares($methods_middlewares["middlewares"], $arguments)) return;
                }
            }
            if($key === "except") {
                foreach($array as $methods_middlewares) {
                    if($find_method_exist($name, $methods_middlewares)) continue;
                    if(!$execute_middlewares($methods_middlewares["middlewares"], $arguments)) return;
                }
            }
        }
        
        return call_user_func_array([$this, $name], $arguments);
    }

    protected function only(array|string $methods):void {
        if(is_string($methods)) $only = [$methods];
        else $only = $methods;

        $this->middleware_apply["only"][] = ["methods" => $only, "middlewares" => $this->middleware_stack];
    }

    protected function all():void {
        $this->middleware_apply["all"] = ["middlewares" => $this->middleware_stack];
    }

    protected function except(array|string $methods):void {
        if(is_string($methods)) $except = [$methods];
        else $except = $methods;

        $this->middleware_apply["except"][] = ["methods" => $except, "middlewares" => $this->middleware_stack];
    }
}