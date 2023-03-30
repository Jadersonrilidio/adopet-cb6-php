<?php

declare(strict_types=1);

/**
 * @return mixed
 */
function env(string $varName, mixed $default = null): mixed
{
    return isset($_ENV[$varName]) ? $_ENV[$varName] : $default;
}
