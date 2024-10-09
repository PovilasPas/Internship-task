<?php

declare(strict_types=1);

namespace App\Console\Executor;

use App\Console\Loader\FileLoaderInterface;
use http\Exception\InvalidArgumentException;

class LoaderExecutor implements ExecutorInterface
{
    public function __construct(
        private readonly FileLoaderInterface $loader,
    ) {

    }

    public function execute(): void
    {
        $filePath = trim(readline('Enter a path of a file to be loaded: '));
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException('The specified file does not exist');
        }

        $this->loader->load($filePath);
    }
}
