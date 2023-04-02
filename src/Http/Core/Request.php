<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Http\Core;

use Jayrods\ScubaPHP\Http\Core\Helper\HttpMultipartParser;

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
    private string $contentType;

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
    private array $inputs = [];

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
        $this->contentType = $_SERVER['CONTENT_TYPE'] ?? 'text/html';
        $this->headers = getallheaders();
        $this->sanitizeQueryParams();
        $this->sanitizePostVars();
        $this->handlePutVars();
        $this->sanitizePutVars();
        $this->mergeInputVars();
    }

    /**
     * 
     */
    public function addUriParams(array $keys, array $values): void
    {
        $this->sanitizeUriParams(array_combine($keys, $values));
    }

    /**
     * 
     */
    private function handlePutVars(): void
    {
        if ($this->httpMethod === 'PUT' or $this->httpMethod === 'PATCH') {
            $multipartParser = new HttpMultipartParser();

            $multipartParser->setContentType($this->contentType);

            $stream = fopen("php://input", 'r');

            $multipartParser->parse($stream);

            fclose($stream);

            $data = $multipartParser->get();

            $this->putVars = $data['variables'];
            $this->files = $data['files'];
        }
    }

    /**
     * 
     */
    private function mergeInputVars(): void
    {
        $this->inputs = array_merge($this->postVars, $this->putVars);
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
    public function contentType(): string
    {
        return $this->contentType;
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
    public function inputs(string $param = null): mixed
    {
        $input = $this->inputs;

        if (!is_null($param)) {
            $input = isset($this->inputs[$param]) ? $this->inputs[$param] : null;
        }

        return $input;
    }

    /**
     * //todo create a File object DTO to store data
     */
    public function files(string $param = null): mixed
    {
        $file = $this->files;

        if (!is_null($param)) {
            $file = isset($file[$param]) ? $file[$param] : null;
        }

        return $file;
    }
}
