<?php

declare(strict_types=1);

namespace App\Loader;

use App\IOUtils;

class WordFileLoader implements FileLoaderInterface
{
    public function __construct(private readonly \PDO $connection)
    {

    }

    public function load(string $filePath): void
    {
        $query = "LOAD DATA LOCAL INFILE ? IGNORE INTO TABLE words FIELDS TERMINATED BY '' (word)";
        $this->connection->prepare($query)->execute([$filePath]);
    }
}
