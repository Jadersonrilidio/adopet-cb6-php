<?php

declare(strict_types=1);

use Jayrods\ScubaPHP\Http\Core\{Request, Router};

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

$router = new Router(
    request: new Request(),
    routes: include ROOT_DIR . SLASH . 'config' . SLASH . 'routes.php'
);

$router->handleRequest()->sendResponse();
