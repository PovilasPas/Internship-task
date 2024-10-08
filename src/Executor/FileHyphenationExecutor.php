<?php

declare(strict_types=1);

namespace App\Executor;

use App\Hyphenator\ArrayHyphenator;
use App\IOUtils;

class FileHyphenationExecutor implements ExecutorInterface
{
    public function __construct(private readonly string $filePath, private readonly string $word)
    {

    }

    public function execute(): void
    {
        if(!file_exists($this->filePath)) {
            echo 'The specified file does not exist.' . PHP_EOL;

            return;
        }
        $rules = IOUtils::readFile($this->filePath);
        $hyphenator = new ArrayHyphenator($rules);
        $hyphenated = $hyphenator->hyphenate($this->word);
        echo $hyphenated->getWord() . PHP_EOL;
    }
}
