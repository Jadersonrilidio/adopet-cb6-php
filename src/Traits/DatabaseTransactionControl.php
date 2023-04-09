<?php

namespace Jayrods\ScubaPHP\Traits;

trait DatabaseTransactionControl
{
    /**
     * 
     */
    public function beginTransaction(): bool
    {
        return $this->conn->beginTransaction();
    }

    /**
     * 
     */
    public function commit(): bool
    {
        return $this->conn->commit();
    }

    /**
     * 
     */
    public function rollback(): bool
    {
        return $this->conn->rollback();
    }
}