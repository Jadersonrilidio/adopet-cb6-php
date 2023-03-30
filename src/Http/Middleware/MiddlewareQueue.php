<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Http\Middleware;

use DomainException;
use Jayrods\ScubaPHP\Http\Core\Request;

class MiddlewareQueue
{
    /**
     * 
     */
    private static array $map = [];

    /**
     * 
     */
    private static array $default = [];

    /**
     * 
     */
    private array $middlewares = [];

    /**
     * 
     */
    public function __construct(array $middlewares = [])
    {
        $this->middlewares = array_merge(self::$default, $middlewares);
    }

    /**
     * 
     */
    public static function setMap(array $map): void
    {
        self::$map = $map;
    }

    /**
     * 
     */
    public static function setDefault(array $default): void
    {
        self::$default = $default;
    }

    /**
     * 
     */
    public function addMiddlewares(array $middlewares): void
    {
        array_push($this->middlewares, ...$middlewares);
    }

    /**
     * 
     */
    public function next(Request $request): bool
    {
        if (empty($this->middlewares)) {
            return true;
        }

        $middleware = array_shift($this->middlewares);

        if (!isset(self::$map[$middleware])) {
            throw new DomainException('Problemas ao processar o middleware da requisicao.', 500);
        }

        $middleware = self::$map[$middleware];
        $middleware = new $middleware();

        $queue = $this;
        $next = function ($request) use ($queue) {
            return $queue->next($request);
        };

        return $middleware->handle(
            request: $request,
            next: $next
        );
    }
}
