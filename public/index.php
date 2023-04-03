<?php

declare(strict_types=1);

use Jayrods\ScubaPHP\Http\Core\Request;
use Jayrods\ScubaPHP\Http\Core\Router;
use Jayrods\ScubaPHP\Http\Middleware\MiddlewareQueue;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

$request = new Request();
$middlewareQueue = new MiddlewareQueue();
$routes = include ROOT_DIR . SLASH . 'config' . SLASH . 'routes.php';


$router = new Router($request, $middlewareQueue, $routes);

$router->handleRequest()->sendResponse();
