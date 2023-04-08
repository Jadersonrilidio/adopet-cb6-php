<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Http\Enum;

enum HttpMethod: string
{
    case Get = 'GET';
    case Post = 'POST';
    case Put = 'PUT';
    case Patch = 'PATCH';
    case Delete = 'DELETE';
}
