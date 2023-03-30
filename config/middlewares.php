<?php

declare(strict_types=1);

return array(
    'maintenance' => Jayrods\ScubaPHP\Http\Middleware\MaintenanceMiddleware::class,
    'session' => Jayrods\ScubaPHP\Http\Middleware\SessionMiddleware::class,
    'auth' => Jayrods\ScubaPHP\Http\Middleware\AuthMiddleware::class,
    'guest' => Jayrods\ScubaPHP\Http\Middleware\GuestMiddleware::class,
);
