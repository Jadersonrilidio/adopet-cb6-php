<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Http\Middleware;

use Closure;
use Jayrods\ScubaPHP\Controller\MaintenanceController;
use Jayrods\ScubaPHP\Http\Core\{Request, View};
use Jayrods\ScubaPHP\Http\Middleware\Middleware;
use Jayrods\ScubaPHP\Infrastructure\FlashMessage;

class MaintenanceMiddleware implements Middleware
{
    /**
     * 
     */
    public function handle(Request $request, Closure $next): bool
    {
        $maintenance = env('MAINTENANCE', 'false');

        if ($maintenance === 'true') {
            $this->callMaintenanceController($request);
        }

        return call_user_func($next, $request);
    }

    /**
     * 
     */
    private function callMaintenanceController(Request $request): void
    {
        $controller = new MaintenanceController(
            request: $request,
            view: new View(),
            flashMsg: new FlashMessage()
        );

        $controller->index()->sendResponse();
    }
}
