<?php

namespace App\Support;

use PDO;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (!self::$connection) {
            self::$connection = require __DIR__ . '/../../config/db.php';
        }

        return self::$connection;
    }
}
