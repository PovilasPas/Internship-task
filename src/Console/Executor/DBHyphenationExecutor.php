<?php

declare(strict_types=1);

namespace App\Console\Executor;

use App\Console\Processor\DBProcessor;
use App\IOUtils;
use App\Model\Rule;
use App\Model\Word;
use App\Repository\RuleRepository;
use App\Repository\WordRepository;


class DBHyphenationExecutor implements ExecutorInterface
{
    public function __construct(
        private readonly WordRepository $wordRepository,
        private readonly RuleRepository $ruleRepository,
        private readonly DBProcessor $processor,
        private readonly string $word
    ) {

    }

    public function execute(): void
    {
        $word = $this->wordRepository->getWordByString($this->word);

        if ($word !== null && $word->getHyphenated() !== null) {
            $matchedRules = $this->ruleRepository->getRulesMatchingWord($word);

            IOUtils::printLinesToCLI(
                [
                    'Hyphenated word:',
                    $word->getHyphenated(),
                    'Rules matched:',
                    ...array_map(fn (Rule $rule): string => $rule->getRule(), $matchedRules)
                ]
            );

            return;
        }

        if ($word === null) {
            $word = new Word($this->word);
            $this->wordRepository->insertWord($word);
            $word->setId((int) $this->wordRepository->getLastInsertedId());
        }

        $result = $this->processor->process([$word])[0];

        IOUtils::printLinesToCLI(
            [
                'Hyphenated word:',
                $result->getWord(),
                'Rules matched:',
                ...$result->getPatterns()
            ]
        );
    }
}
