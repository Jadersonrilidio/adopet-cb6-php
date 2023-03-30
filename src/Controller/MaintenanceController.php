<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Http\Core\Response;

class MaintenanceController extends Controller
{
    /**
     * 
     */
    public function index(): Response
    {
        $content = $this->view->renderView(template: 'maintenance');
        $page = $this->view->renderlayout('App Maintenance', $content);

        return new Response($page);
    }
}
