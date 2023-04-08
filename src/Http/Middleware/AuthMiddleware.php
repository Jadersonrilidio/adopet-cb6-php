<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Http\Middleware;

use Closure;
use Jayrods\ScubaPHP\Http\Core\Request;
use Jayrods\ScubaPHP\Http\Core\Router;
use Jayrods\ScubaPHP\Infrastructure\Auth;
use Jayrods\ScubaPHP\Http\Middleware\Middleware;

class AuthMiddleware implements Middleware
{
    /**
     * 
     */
    private Auth $auth;

    /**
     * 
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * 
     */
    public function handle(Request $request, Closure $next): bool
    {
        if (!$this->auth->authUser()) {
            Router::redirect('login');
        }

        return call_user_func($next, $request);
    }
}
