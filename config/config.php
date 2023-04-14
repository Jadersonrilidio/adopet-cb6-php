<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Jayrods\ScubaPHP\Http\Middleware\MiddlewareQueue;

// Global constants definition
define('ROOT_DIR', dirname(__DIR__));
define('SLASH', DIRECTORY_SEPARATOR);

// Global directory paths constants
define('CONFIG_DIR', ROOT_DIR . SLASH . 'config' . SLASH);
define('RESOURCES_PATH', ROOT_DIR . SLASH . 'resources' . SLASH);
define('VIEW_PATH', RESOURCES_PATH . 'view' . SLASH);
define('COMPONENT_PATH', VIEW_PATH . 'components' . SLASH);
define('LAYOUT_PATH', VIEW_PATH . 'layout' . SLASH);
define('DATABASE_PATH', ROOT_DIR . SLASH . 'database' . SLASH);
define('STORAGE_DIR', ROOT_DIR . SLASH . 'storage' . SLASH);
define('CACHE_DIR', STORAGE_DIR . 'cache' . SLASH);

define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');

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
MiddlewareQueue::setMap(map: include CONFIG_DIR . 'middlewares.php');
MiddlewareQueue::setDefault(default: ['maintenance', 'session']);
