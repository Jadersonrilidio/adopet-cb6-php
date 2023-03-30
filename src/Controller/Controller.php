<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller;

use Jayrods\ScubaPHP\Http\Core\{Request, View};
use Jayrods\ScubaPHP\Infrastructure\FlashMessage;

abstract class Controller
{
    /**
     * 
     */
    protected Request $request;

    /**
     * 
     */
    protected View $view;

    /**
     * 
     */
    protected FlashMessage $flashMsg;

    /**
     * 
     */
    public function __construct(Request $request, View $view, FlashMessage $flashMsg)
    {
        $this->request = $request;
        $this->view = $view;
        $this->flashMsg = $flashMsg;
    }
}
