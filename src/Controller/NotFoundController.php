<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Http\Core\Response;

class NotFoundController extends Controller
{
    /**
     * 
     */
    public function index(): Response
    {
        $content = $this->view->renderView(template: 'not_found');

        $page = $this->view->renderlayout('404 - Not Found', $content);

        return new Response(
            content: $page,
            httpCode: 404
        );
    }
}
