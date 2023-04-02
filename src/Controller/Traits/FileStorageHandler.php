<?php

namespace Jayrods\ScubaPHP\Controller\Traits;

trait FileStorageHandler
{
    /**
     * 
     */
    public function storeFile(array $fileData): int|bool
    {
        $from = $fileData['tmp_name'];
        $to = RESOURCES_PATH . 'img' . SLASH . $fileData['name'];

        $content = file_get_contents($from);

        return file_put_contents($to, $content);
    }
}
