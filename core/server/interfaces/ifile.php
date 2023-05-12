<?php
namespace core\server\interfaces;

interface IFile {
    public function put(string $path):void;
}