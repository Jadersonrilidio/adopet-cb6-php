<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Http\Core;

use Jayrods\ScubaPHP\Infrastructure\Helper\HttpParser;
use Jayrods\ScubaPHP\Http\Enum\HttpMethod;

class Request
{
    /**
     * 
     */
    private HttpMethod $httpMethod;

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
        $this->run();
    }

    /**
     * 
     */
    public function run(): void
    {
        $this->httpMethod = HttpMethod::tryFrom($_SERVER['REQUEST_METHOD']) ?? HttpMethod::Get;
        $this->uri = $this->sanitizedUri();
        $this->contentType = $_SERVER['CONTENT_TYPE'] ?? 'text/html';
        $this->headers = getallheaders();
        $this->files = $this->handleFiles($_FILES);
        $this->handleQueryParams();
        $this->handlePostVars();
        $this->handlePutVars();
    }

    /**
     * 
     */
    public function addUriParams(array $keys, array $values): void
    {
        $this->handleUriParams(array_combine($keys, $values));
    }

    /**
     * 
     */
    private function sanitizedUri(): string
    {
        return isset($_SERVER['PATH_INFO'])
            ? filter_input(INPUT_SERVER, 'PATH_INFO', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            : '/';
    }

    /**
     * 
     */
    private function handleUriParams(array $params): void
    {
        foreach ($params as $key => $value) {
            $var = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $this->uriParams[$key] = !ctype_space($var) ? $var : '';
        }
    }

    /**
     * 
     */
    private function handleQueryParams(): void
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
    private function handlePostVars(): void
    {
        $paramKeys = array_keys($_POST);

        foreach ($paramKeys as $param) {
            $postVar = filter_input(INPUT_POST, $param, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $this->inputs[$param] = !ctype_space($postVar) ? $postVar : '';
        }
    }

    /**
     * 
     */
    private function handleFiles(?array $files = null): array
    {
        if (empty($files) or is_null($files)) {
            return [];
        }

        $files = array_map(function ($file) {
            $extension = explode('.', $file['name'])[1];
            $hashedName = 'upload_' . hash('md5', uniqid() . time());

            $file['hashname'] = $hashedName . '.' . $extension;

            return $file;
        }, $files);

        return $files ?? [];
    }

    /**
     * 
     */
    private function handlePutVars(): void
    {
        //test: does this conditional expression works fine?
        if ($this->httpMethod === HttpMethod::Put or HttpMethod::Patch) {
            $multipartParser = new HttpParser();

            $multipartParser->setContentType($this->contentType);

            $stream = fopen("php://input", 'r');

            $multipartParser->parse($stream);

            fclose($stream);

            $data = $multipartParser->get();

            $this->handleInputs($data['variables']);
            $this->files = $this->handleFiles($data['files']);
        }
    }

    /**
     * 
     */
    private function handleInputs(array $variables): void
    {
        foreach ($variables as $key => $value) {
            $value = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $this->inputs[$key] = !ctype_space($value) ? $value : '';
        }
    }

    /**
     * 
     */
    public function httpMethod(): string
    {
        return $this->httpMethod->value;
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
    public function headers(string $param = 'all'): array|string|null
    {
        $header = $this->headers;

        if (!is_null($param)) {
            $header = isset($this->headers[$param]) ? $this->headers[$param] : null;
        }

        return $header;
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
     * 
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
