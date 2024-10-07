<?php

declare(strict_types=1);

namespace App\DB;

class ConnectionManager
{
    private static ?\PDO $connection = null;

    public static function getConnection(): \PDO
    {
        if (self::$connection === null) {
            $host = getenv('DB_HOST');
            $port = getenv('DB_PORT');
            $name = getenv('DB_NAME');
            $user = getenv('DB_USER');
            $pass = getenv('DB_PASS');

            $connection = new \PDO(
                "mysql:host=$host;port=$port;dbname=$name",
                $user,
                $pass,
                [\PDO::MYSQL_ATTR_LOCAL_INFILE => true]
            );

            $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$connection = $connection;
        }
        return self::$connection;
    }
}
