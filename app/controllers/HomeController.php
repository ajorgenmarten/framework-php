<?php
namespace app\controllers;

use app\middlewares\Check;
use core\base\ControllerBase;
use core\server\Request;
use core\server\Response;

class HomeController extends ControllerBase {
    function __construct()
    {
        $this->middleware(Check::class)->all();
        $this->middleware(Check::class)->only("index");
    }
    function index(Request $request, Response $response) {        
        $response->json($request);
    }
    function test_middleware(Request $request, Response $response) {
        $response->json(new Class() {public $name = "alejandro";});
    }
    function respond_image(Request $request, Response $response) {
        $response->send_file("./app/files/assassins_creed.jpg");
    }
}