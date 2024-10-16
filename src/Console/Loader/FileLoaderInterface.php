<?php

declare(strict_types=1);

namespace App\Console\Loader;

interface FileLoaderInterface
{
    public function load(string $filePath): void;
}
