<?php
namespace core;

class Autoload {
    function __construct()
    {
        $this->loadClasses();
        $this->loadVendor();
    }
    private function loadClasses() {
        spl_autoload_register(function ($class_name) {
            $class_name = preg_replace("/\\\\/", "/", $class_name);
            $file = "$class_name.php";
            if(file_exists($file)) require_once $file;
        });
    }
    private function loadVendor() {
        if(file_exists("vendor/autoload.php")) require_once "vendor/autoload.php";
    }
}