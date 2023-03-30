<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Http\Core;

class Request
{
    /**
     * 
     */
    private string $httpMethod;

    /**
     * 
     */
    private string $uri;

    /**
     * 
     */
    private array $headers = [];

    /**
     * 
     */
    private array $uriParams = [];

    /**
     * 
     */
    private array $queryParams = [];

    /**
     * 
     */
    private array $postVars = [];

    /**
     * 
     */
    public function __construct()
    {
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $_SERVER['PATH_INFO'] ?? '/';
        $this->headers = getallheaders();
        $this->sanitizeQueryParams();
        $this->sanitizePostVars();
    }

    /**
     * 
     */
    public function addUriParams(array $keys, array $values)
    {
        $this->sanitizeUriParams(array_combine($keys, $values));
    }

    /**
     * 
     */
    private function sanitizeUriParams(array $params): void
    {
        foreach ($params as $key => $value) {
            $var = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $this->uriParams[$key] = !ctype_space($var) ? $var : '';
        }
    }

    /**
     * 
     */
    private function sanitizeQueryParams(): void
    {
        $paramKeys = array_keys($_GET);

        foreach ($paramKeys as $param) {
            $queryParam = filter_input(INPUT_GET, $param, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $this->queryParams[$param] = !ctype_space($queryParam) ? $queryParam : '';
        }
    }

    /**
     * 
     */
    private function sanitizePostVars(): void
    {
        $paramKeys = array_keys($_POST);

        foreach ($paramKeys as $param) {
            $postVar = filter_input(INPUT_POST, $param, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $this->postVars[$param] = !ctype_space($postVar) ? $postVar : '';
        }
    }

    /**
     * 
     */
    public function httpMethod(): string
    {
        return $this->httpMethod;
    }

    /**
     * 
     */
    public function uri(): string
    {
        return $this->uri;
    }

    /**
     * 
     */
    public function headers(string $header = 'all'): array
    {
        return $header === 'all' ? $this->headers : $this->headers[$header];
    }

    /**
     * 
     */
    public function uriParams(string $param = 'all'): mixed
    {
        return $param === 'all' ? $this->uriParams : $this->uriParams[$param];
    }

    /**
     * 
     */
    public function queryParams(string $param = 'all'): mixed
    {
        return $param === 'all' ? $this->queryParams : $this->queryParams[$param];
    }

    /**
     * 
     */
    public function postVars(string $param = 'all'): mixed
    {
        return $param === 'all' ? $this->postVars : $this->postVars[$param];
    }
}
