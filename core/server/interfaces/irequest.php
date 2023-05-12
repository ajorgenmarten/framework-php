<?php
namespace core\server\interfaces;

interface IRequest {
    
}

interface IQuery {
    function __get($name);
}