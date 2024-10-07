<?php

declare(strict_types=1);

namespace App\Console\Loader;

use App\Repository\WordRepository;

class WordFileLoader implements FileLoaderInterface
{
    public function __construct(
        private readonly WordRepository $wordRepository,
    ) {

    }

    public function load(string $filePath): void
    {
        $this->wordRepository->loadWordsFromFile($filePath);
    }
}
