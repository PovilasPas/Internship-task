<?php

declare(strict_types=1);

namespace App\Console\Executor;

use App\Console\Hyphenator\HyphenatorInterface;
use App\IOUtils;

class FileHyphenationExecutor implements ExecutorInterface
{
    private ?string $word;

    public function __construct(
        private readonly HyphenatorInterface $hyphenator,
    ) {

    }

    public function setWord(string $word): void
    {
        $this->word = $word;
    }

    public function execute(): void
    {
        if ($this->word === null) {
            throw new \BadMethodCallException('Word should be set before calling execute() method');
        }

        $hyphenated = $this->hyphenator->hyphenate($this->word);
        IOUtils::printLinesToCLI(
            [
                'Hyphenated word:',
                $hyphenated->getWord(),
            ]
        );

        $this->word = null;
    }
}
