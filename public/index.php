<?php

chdir( dirname(__DIR__) );

require_once "./core/autoload.php";

use core\Autoload;
use core\server\Server;

new Autoload();

$server = new Server();

$server->init();