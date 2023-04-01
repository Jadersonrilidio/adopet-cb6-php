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
    private array $putVars = [];

    /**
     * 
     */
    private array $files = [];

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

        if ($this->httpMethod === 'PUT' or $this->httpMethod === 'PATCH') {
            $this->extractPutVars();
            $this->sanitizePutVars();
        }
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
    private function extractPutVars(): void
    {
        $putData = fopen("php://input", 'r');
        $content = stream_get_contents($putData);
        fclose($putData);

        $content = str_replace('%20', ' ', $content);
        $content = str_replace('%40', '@', $content);
        $content = explode("&", $content);

        foreach ($content as $line) {
            $keyValue = explode("=", $line);
            $this->putVars[$keyValue[0]] = $keyValue[1];
        }
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
    private function sanitizePutVars(): void
    {
        foreach ($this->putVars as $key => $value) {
            $value = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $this->putVars[$key] = !ctype_space($value) ? $value : '';
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
    public function uriParams(string $param = null): mixed
    {
        $uriParam = $this->uriParams;

        if (!is_null($param)) {
            $uriParam = isset($this->uriParams[$param]) ? $this->uriParams[$param] : null;
        }

        return $uriParam;
    }

    /**
     * 
     */
    public function queryParams(string $param = null): mixed
    {
        $queryParam = $this->queryParams;

        if (!is_null($param)) {
            $queryParam = isset($this->queryParams[$param]) ? $this->queryParams[$param] : null;
        }

        return $queryParam;
    }

    /**
     * 
     */
    public function postVars(string $param = null): mixed
    {
        $postVar = $this->postVars;

        if (!is_null($param)) {
            $postVar = isset($this->postVars[$param]) ? $this->postVars[$param] : null;
        }

        return $postVar;
    }

    /**
     * 
     */
    public function putVars(string $param = null): mixed
    {
        $putVar = $this->putVars;

        if (!is_null($param)) {
            $putVar = isset($this->putVars[$param]) ? $this->putVars[$param] : null;
        }

        return $putVar;
    }

    /**
     * //todo
     */
    public function files(string $param = null): mixed
    {
        $file = $this->files;

        if (!is_null($param)) {
            $file = isset($this->files[$param]) ? $this->files[$param] : null;
        }

        return $file;
    }
}
