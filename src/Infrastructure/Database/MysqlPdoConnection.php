<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Infrastructure\Database;

use Jayrods\ScubaPHP\Infrastructure\Database\PdoConnection;
use PDO;

class MysqlPdoConnection extends PdoConnection
{
    /**
     * 
     */
    protected function connect(): void
    {
        $host = env('DB_HOST', 'database.sqlite');
        $port = env('DB_PORT', 'database.sqlite');
        $dbname = env('DB_NAME', 'database.sqlite');

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
        
        $user = env('DB_USER', 'database.sqlite');
        $password = env('DB_PASSWORD', 'database.sqlite');

        self::$connection = new PDO($dsn, $user, $password);

        unset($host, $port, $dbname, $dsn, $user, $password);
    }
}
