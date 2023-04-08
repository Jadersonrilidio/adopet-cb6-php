<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Http\Core;

use Jayrods\ScubaPHP\Http\Core\Request;
use Jayrods\ScubaPHP\Http\Core\Response;
use Jayrods\ScubaPHP\Http\Middleware\MiddlewareQueue;
use Jayrods\ScubaPHP\Traits\JsonCache;
use Psr\Container\ContainerInterface;

class Router
{
    use JsonCache;

    /**
     * 
     */
    private Request $request;

    /**
     * 
     */
    private MiddlewareQueue $middlewareQueue;

    /**
     * 
     */
    private ContainerInterface $diContainer;

    /**
     * 
     */
    private array $routes;

    /**
     * 
     */
    public function __construct(Request $request, MiddlewareQueue $middlewareQueue, ContainerInterface $diContainer, array $routes)
    {
        $this->request = $request;
        $this->middlewareQueue = $middlewareQueue;
        $this->diContainer = $diContainer;
        $this->routes = $routes;
    }

    /**
     * 
     */
    public function handleRequest(): Response
    {
        $routeParams = $this->routeParams();

        $controller = $routeParams[0];
        $method = $routeParams[1];
        $middlewares = $routeParams[2] ?? [];

        $this->executeMiddlewaresQueue($middlewares);

        $controller = $this->diContainer->get($controller);

        return $controller->$method($this->request);
    }

    /**
     * 
     */
    private function routeParams()
    {
        $httpMethod = $this->request->httpMethod();
        $uri = $this->request->uri();

        $routeRegexArray = $this->getJsonCache('routeRegexArray') ?? $this->createRouteRegexArray($httpMethod);

        $requestedRoute = "$httpMethod|$uri";

        foreach ($routeRegexArray as $route => $regex) {
            if (preg_match($regex, $requestedRoute, $uriParamValues)) {
                if (preg_match_all('/\{([^\/]+?)\}/', $route, $uriParamKeys)) {
                    unset($uriParamValues[0]);

                    $this->request->addUriParams($uriParamKeys[1], $uriParamValues);
                }

                return $this->routes[$route];
            }
        }

        return $this->routes['fallback'];
    }

    /**
     * 
     */
    private function createRouteRegexArray(): array
    {
        // Mount route-regex array structure
        $regexArray = array_combine(array_keys($this->routes), array_keys($this->routes));

        // Replace URI params by regex group
        $regexArray = preg_replace('/\{.+?\}/', '([^/]+?)', $regexArray);

        // Format regex expression slashes
        $regexArray = str_replace('/', '\/', $regexArray);

        // Format regex expression slashes
        $regexArray = str_replace('|', '\|', $regexArray);

        // wrap regex expression with start and end signs
        $regexArray = array_map(function ($route) {
            return '/^' . $route . '$/';
        }, $regexArray);

        $this->storeJsonCache($regexArray, 'routeRegexArray');

        return $regexArray;
    }

    /**
     * 
     */
    private function executeMiddlewaresQueue(array $middlewares): bool
    {
        $this->middlewareQueue->addMiddlewares($middlewares);

        return $this->middlewareQueue->next($this->request);
    }

    /**
     * 
     */
    public static function redirect(string $path = ''): void
    {
        header("Location: " . SLASH . $path);
        exit;
    }
}
