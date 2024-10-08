<?php

declare(strict_types=1);

namespace App\Console\Executor;

use App\Console\Hyphenator\HyphenatorInterface;
use App\IOUtils;

class FileHyphenationExecutor implements ExecutorInterface
{
    public function __construct(
        private readonly string $filePath,
        private readonly HyphenatorInterface $hyphenator,
        private readonly string $word
    ) {

    }

    public function execute(): void
    {
        if(!file_exists($this->filePath)) {
            throw new \InvalidArgumentException("The specified file does not exist.");
        }
        $hyphenated = $this->hyphenator->hyphenate($this->word);
        IOUtils::printLinesToCLI(
            [
                'Hyphenated word:',
                $hyphenated->getWord(),
            ]
        );
    }
}
