<?php

declare(strict_types=1);

use Jayrods\ScubaPHP\Http\Core\Request;
use Jayrods\ScubaPHP\Http\Core\Router;
use Jayrods\ScubaPHP\Http\Middleware\MiddlewareQueue;
use Psr\Container\ContainerInterface;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

/** @var ContainerInterface */
$diContainer = require ROOT_DIR . SLASH . 'config' . SLASH . 'dependencies.php';

$routes = require ROOT_DIR . SLASH . 'config' . SLASH . 'routes.php';

$router = new Router(
    request: new Request(),
    middlewareQueue: new MiddlewareQueue(),
    diContainer: $diContainer,
    routes: $routes 
);

$router->handleRequest()->sendResponse();
