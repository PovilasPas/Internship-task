<?php

declare(strict_types=1);

namespace App\Loader;

use App\Repository\WordRepository;

class WordFileLoader implements FileLoaderInterface
{
    public function __construct(private readonly \PDO $connection)
    {

    }

    public function load(string $filePath): void
    {
        $repository = new WordRepository($this->connection);
        $repository->loadWordsFromFile($filePath);
    }
}
