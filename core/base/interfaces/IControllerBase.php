<?php
namespace core\base\interfaces;

interface IControllerBase {
    function _call(string $name, array $arguments);
}