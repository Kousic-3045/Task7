<?php

declare(strict_types=1);

class Database
{
    private static ?mysqli $connection = null;

    public static function connection(): mysqli
    {
        if (self::$connection === null) {
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $user = getenv('DB_USER') ?: 'root';
            $password = getenv('DB_PASSWORD') ?: '';
            $database = getenv('DB_NAME') ?: 'validation_demo';
            $port = (int)(getenv('DB_PORT') ?: 3306);

            self::$connection = new mysqli($host, $user, $password, $database, $port);
            if (self::$connection->connect_errno) {
                throw new RuntimeException('Database connection failed: ' . self::$connection->connect_error);
            }
            self::$connection->set_charset('utf8mb4');
        }
        return self::$connection;
    }
}
