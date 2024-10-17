<?php

declare(strict_types=1);

namespace Test\APITests;

use App\Database\ConnectionManager;
use PHPUnit\Framework\TestCase;

abstract class ApiTest extends TestCase
{
    protected static \PDO $connection;

    protected static string $host;

    public static function setUpBeforeClass(): void
    {
        self::$host = 'http://localhost:8000';
        self::$connection = ConnectionManager::getConnection();
    }

    protected function tearDown(): void
    {
        $query = 'SET FOREIGN_KEY_CHECKS=0;'
            . 'TRUNCATE TABLE words;'
            . 'SET FOREIGN_KEY_CHECKS=1;';

        self::$connection->exec($query);
    }
}
