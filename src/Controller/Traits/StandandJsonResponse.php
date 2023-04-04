<?php

namespace Jayrods\ScubaPHP\Controller\Traits;

use Jayrods\ScubaPHP\Http\Core\JsonResponse;
use Jayrods\ScubaPHP\Infrastructure\ErrorMessage;

trait StandandJsonResponse
{
    /**
     * 
     */
    public function errorJsonResponse(string $message, int $httpCode = 400): JsonResponse
    {
        return new JsonResponse(['error' => $message], $httpCode);
    }

    /**
     * 
     */
    public function errorMessagesJsonResponse(): JsonResponse
    {
        return new JsonResponse(['errors' => ErrorMessage::errorMessages()], 400);
    }

    /**
     * 
     */
    public function notFoundJsonResponse(string $message = 'Not found'): JsonResponse
    {
        return new JsonResponse(['error' => $message], 404);
    }
}
