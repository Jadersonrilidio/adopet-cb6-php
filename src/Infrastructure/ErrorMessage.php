<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Infrastructure;

use JsonSerializable;

class ErrorMessage implements JsonSerializable
{
    /**
     * 
     */
    private static array $errors = [];

    /**
     * 
     */
    public static function add(string $field, string $message)
    {
        self::$errors[$field][] = $message;
    }

    /**
     * 
     */
    public static function errorMessages(): array
    {
        return self::$errors;
    }

    /**
     * 
     */
    public function jsonSerialize(): mixed
    {
        return ['errors' => self::$errors];
    }
}
