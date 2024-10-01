<?php

declare(strict_types=1);

namespace App\Loader;

use App\IOUtils;

class RuleFileLoader implements FileLoaderInterface
{
    public function __construct(private readonly \PDO $connection)
    {

    }

    public function load(string $filePath): void
    {
        $query = 'DELETE FROM rules';
        $this->connection->prepare($query)->execute();

        $query = 'LOAD DATA LOCAL INFILE ? IGNORE INTO TABLE rules FIELDS TERMINATED BY \'\' (rule)';
        $this->connection->prepare($query)->execute([$filePath]);

        $query = "UPDATE words SET hyphenated = NULL";
        $this->connection->prepare($query)->execute();
    }
}
