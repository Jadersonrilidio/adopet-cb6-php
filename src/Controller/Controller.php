<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller;

use Jayrods\ScubaPHP\Http\Core\View;
use Jayrods\ScubaPHP\Infrastructure\FlashMessage;

abstract class Controller
{
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
    public function __construct(View $view, FlashMessage $flashMsg)
    {
        $this->view = $view;
        $this->flashMsg = $flashMsg;
    }
}
