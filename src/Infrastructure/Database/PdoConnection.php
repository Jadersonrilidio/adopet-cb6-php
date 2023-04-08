<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Infrastructure\Database;

use PDO;

abstract class PdoConnection
{
    /**
     * 
     */
    protected PDO $connection;

    /**
     * 
     */
    public function __construct()
    {
        $this->connect();
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
        return $this->connection;
    }
}