<?php

declare(strict_types=1);

namespace App\Core;

use mysqli;
use mysqli_sql_exception;
use RuntimeException;

final class Database
{
    public static function connection(): mysqli
    {
        static $connection = null;

        if ($connection instanceof mysqli) {
            return $connection;
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $hostname = getenv('DB_HOST') ?: 'localhost';
        $dbuser = getenv('DB_USER') ?: 'root';
        $dbPassword = getenv('DB_PASSWORD') ?: '';
        $dbname = getenv('DB_NAME') ?: 'university_portal';

        try {
            $connection = new mysqli($hostname, $dbuser, $dbPassword, $dbname);
            $connection->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $exception) {
            throw new RuntimeException('Database connection failed: ' . $exception->getMessage());
        }

        return $connection;
    }
}
