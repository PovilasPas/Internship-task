<?php

declare(strict_types=1);

namespace App\Executor;

use App\Loader\FileLoaderInterface;

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
            echo 'The specified file does not exist.' . PHP_EOL;

            return;
        }
        $this->loader->load($filePath);
    }
}
