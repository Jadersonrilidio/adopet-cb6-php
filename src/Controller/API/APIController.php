<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller;

use Jayrods\ScubaPHP\Http\Core\Request;

abstract class APIController
{
    /**
     * 
     */
    protected Request $request;

    /**
     * 
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
