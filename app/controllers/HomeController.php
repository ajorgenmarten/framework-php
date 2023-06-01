<?php
namespace app\controllers;

use app\middlewares\Check;
use core\base\ControllerBase;
use core\server\Request;
use core\server\Response;

class HomeController extends ControllerBase {
    
    function index(Request $req, Response $res) {
        $res->json("Welcome to this simple framework!");
    }
}