<?php

namespace Jayrods\ScubaPHP\Http\Enum;

use BackedEnum;

enum HttpMethod: string implements BackedEnum
{
    case Get = 'GET';
    case Post = 'POST';
    case Put = 'PUT';
    case Patch = 'PATCH';
    case Delete = 'DELETE';
}
