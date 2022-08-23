<?php

namespace App\Services\Database;

use PDO;

abstract class Context {

    protected PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function disconnect()
    {
        $this->connection = null;
    }
}

