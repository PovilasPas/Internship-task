<?php

declare(strict_types=1);

namespace App\Console\Executor;

use App\Console\Hyphenator\ArrayHyphenator;
use App\Console\Processor\DatabaseProcessor;
use App\Model\Rule;
use App\Repository\MatchRepository;
use App\Repository\RuleRepository;
use App\Repository\WordRepository;

class HyphenateNotHyphenatedExecutor implements ExecutorInterface
{
    public function __construct(
        private readonly WordRepository $wordRepository,
        private readonly DatabaseProcessor $processor,
    ) {

    }

    public function execute(): void
    {
        $notHyphenated = $this->wordRepository->getWordsWithoutHyphenation();

        $this->processor->process($notHyphenated);
    }
}
