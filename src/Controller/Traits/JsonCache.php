<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\Traits;

trait JsonCache
{
    /**
     * 
     */
    public static function getJsonCache(string $file): ?array
    {
        if (!$jsonCache = file_get_contents(CACHE_DIR . $file . '.json')) {
            return null;
        }

        $cache = json_decode($jsonCache, true);

        $expired = (time() - $cache['timestamp']) > CACHE_EXPIRATION_TIME;

        return !$expired ? $cache['content'] : null;
    }

    /**
     * 
     */
    public static function storeJsonCache(mixed $content, string $file): int|false
    {
        $cache = array(
            'timestamp' => time(),
            'content' => $content
        );

        $jsonContent = json_encode($cache);

        return file_put_contents(CACHE_DIR . $file . '.json', $jsonContent);
    }
}
