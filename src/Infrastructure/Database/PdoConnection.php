<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Infrastructure\Database;

use PDO;

/**
 * Provides a Singleton PDO conenction.
 */
abstract class PdoConnection
{
    /**
     * 
     */
    protected static PDO $connection;

    /**
     * 
     */
    public function __construct()
    {
        if (!isset(self::$connection)) {
            $this->connect();
            $this->setAttributes();
        }
    }

    /**
     * 
     */
    private function setAttributes(): void
    {
        $errMode = match (ENVIRONMENT) {
            'production' => PDO::ERRMODE_SILENT,
            'development' => PDO::ERRMODE_EXCEPTION,
            'test' => PDO::ERRMODE_WARNING
        };

        self::$connection->setAttribute(
            PDO::ATTR_ERRMODE,
            $errMode
        );
    }

    /**
     * 
     */
    abstract protected function connect(): void;

    /**
     * 
     */
    public function getConnection(): PDO
    {
        return self::$connection;
    }
}
