<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Http\Middleware;

use Closure;
use Jayrods\ScubaPHP\Http\Core\Request;

interface Middleware
{
    /**
     * 
     */
    public function handle(Request $request, Closure $next): bool;
}
