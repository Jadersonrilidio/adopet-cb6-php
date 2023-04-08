<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\Traits;

trait FileStorageHandler
{
    /**
     * 
     */
    public function storeFile(array $fileData): bool
    {
        $from = $fileData['tmp_name'];
        $to = RESOURCES_PATH . 'img' . SLASH . $fileData['hashname'];

        $result = copy($from, $to);

        unlink($from);

        return $result;
    }

    /**
     * 
     */
    public function deleteFile(?string $file = null): bool
    {
        if (!is_null($file)) {
            return unlink(RESOURCES_PATH . 'img' . SLASH . $file);
        }

        return false;
    }
}
