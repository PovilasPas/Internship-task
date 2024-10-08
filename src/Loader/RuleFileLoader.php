<?php

declare(strict_types=1);

namespace App\Loader;

use App\Repository\RuleRepository;

class RuleFileLoader implements FileLoaderInterface
{
    public function __construct(private readonly \PDO $connection)
    {

    }

    public function load(string $filePath): void
    {
        $repository = new RuleRepository($this->connection);
        $repository->loadRulesFromFile($filePath);
    }
}
