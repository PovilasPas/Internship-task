<?php

declare(strict_types=1);

namespace App\Console\Executor;

use App\Console\Processor\DatabaseProcessor;
use App\IOUtils;
use App\Model\Rule;
use App\Model\Word;
use App\Repository\RuleRepository;
use App\Repository\WordRepository;


class DatabaseHyphenationExecutor implements ExecutorInterface
{
    private string $word;

    public function __construct(
        private readonly WordRepository $wordRepository,
        private readonly RuleRepository $ruleRepository,
        private readonly DatabaseProcessor $processor,
    ) {

    }

    public function setWord(string $word): void
    {
        $this->word = $word;
    }

    public function execute(): void
    {
        if (!isset($this->word)) {
            throw new \LogicException('Word should be set before calling execute() method');
        }

        $word = $this->wordRepository->getWordByString($this->word);

        if ($word !== null && $word->getHyphenated() !== null) {
            $matchedRules = $this->ruleRepository->getRulesMatchingWord($word);

            IOUtils::printLinesToCLI(
                [
                    'Hyphenated word:',
                    $word->getHyphenated(),
                    'Rules matched:',
                    ...array_map(fn (Rule $rule): string => $rule->getRule(), $matchedRules),
                ]
            );
            unset($this->word);

            return;
        }

        if ($word === null) {
            $word = new Word($this->word);
            $this->wordRepository->insertWord($word);
            $word->setId((int) $this->wordRepository->getLastInsertedId());
        }

        $result = $this->processor->process([$word])[0];

        IOUtils::printLinesToCli(
            [
                'Hyphenated word:',
                $result->getWord(),
                'Rules matched:',
                ...$result->getPatterns(),
            ]
        );

        unset($this->word);
    }
}
