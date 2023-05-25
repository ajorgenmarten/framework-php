<?php
namespace core\base\abstracts;

use core\base\interfaces\IControllerBase;

abstract class AbstractControllerBase {
    abstract protected function middleware(array|string $midlleware):IControllerBase;
    abstract function _call(string $name, array $arguments);
    abstract protected function all():void;
    abstract protected function only(array|string $methods):void;
    abstract protected function except(array|string $methods):void;
}
