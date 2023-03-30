<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Jayrods\ScubaPHP\Http\Middleware\MiddlewareQueue;

// Global constants definition
define('ROOT_DIR', dirname(__DIR__));
define('SLASH', DIRECTORY_SEPARATOR);

define('VIEW_PATH', ROOT_DIR . SLASH . 'resources' . SLASH . 'view' . SLASH);
define('COMPONENT_PATH', VIEW_PATH . 'components' . SLASH);
define('LAYOUT_PATH', VIEW_PATH . 'layout' . SLASH);
define('DATABASE_PATH', ROOT_DIR . SLASH . 'database' . SLASH);
define('CACHE_DIR', ROOT_DIR . SLASH . 'storage' . SLASH . 'cache' . SLASH);

// Global flash message constants
define('FLASH', 'flash_message');

// Environment variables loading
$dotenv = Dotenv::createImmutable(
    paths: ROOT_DIR
);

$dotenv->load();

// .env global constants definition
define('APP_URL', env('APP_URL', 'http://localhost:8000'));
define('ENVIRONMENT', env('ENVIRONMENT', 'production'));
define('CACHE_EXPIRATION_TIME', env('CACHE_EXPIRATION_TIME', 30));

// Middlewares mapping and settings
MiddlewareQueue::setMap(
    map: include ROOT_DIR . SLASH . 'config' . SLASH . 'middlewares.php'
);

MiddlewareQueue::setDefault(
    default: array(
        'maintenance',
        'session'
    )
);
