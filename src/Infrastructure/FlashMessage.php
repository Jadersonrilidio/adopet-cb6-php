<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Infrastructure;

class FlashMessage
{
    /**
     * 
     */
    private static array $next = [];

    /**
     * 
     */
    private static array $previous = [];

    /**
     * 
     */
    public function __construct()
    {
        if (isset($_SESSION[FLASH])) {
            self::$previous = $_SESSION[FLASH];
            unset($_SESSION[FLASH]);
        }
    }

    /**
     * 
     */
    public function __destruct()
    {
        $_SESSION[FLASH] = self::$next;
    }

    /**
     * 
     */
    public function set(array $messages): void
    {
        foreach ($messages as $name => $message) {
            self::$next[$name] = $message;
        }
    }

    /**
     * 
     */
    public function add(array $messages): void
    {
        foreach ($messages as $name => $message) {
            self::$next[$name][] = $message;
        }
    }

    /**
     * 
     */
    public function get(string $name): string
    {
        return self::$previous[$name] ?? '';
    }

    /**
     * 
     */
    public function getArray(string $name): array
    {
        return self::$previous[$name] ?? [];
    }
}
