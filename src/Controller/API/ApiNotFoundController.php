<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\API;

use Jayrods\ScubaPHP\Controller\API\ApiController;
use Jayrods\ScubaPHP\Http\Core\JsonResponse;
use Jayrods\ScubaPHP\Http\Core\Request;

class ApiNotFoundController extends ApiController
{
    /**
     * 
     */
    public function notFound(Request $request): JsonResponse
    {
        $content = ['error' => 'resource not found.'];

        return new JsonResponse($content, 404);
    }
}
