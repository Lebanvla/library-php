<?php
require_once __DIR__  . "/../vendor/autoload.php";

use Common\Config;

Config::initialize();

if (Config::getMode() === "development") {
    ini_set("error_reporting", E_ALL);
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
}

use Common\Router;

$router = new Router();

try {
    $router->run();
} catch (Exception $e) {
    header("HTTP/1.1 404 Not Found");
}
