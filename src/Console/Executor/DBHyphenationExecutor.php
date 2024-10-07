<?php

declare(strict_types=1);

namespace App\Console\Executor;

use App\Console\Processor\DBProcessor;
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

            echo $word->getHyphenated() . PHP_EOL;
            foreach ($matchedRules as $rule) {
                echo 'Matched rule: ' . $rule->getRule() . PHP_EOL;
            }

            return;
        }

        if ($word === null) {
            $word = new Word($this->word);
            $this->wordRepository->insertWord($word);
            $word->setId((int) $this->wordRepository->getLastInsertedId());
        }

        $result = $this->processor->process([$word])[0];

        echo $result->getWord() . PHP_EOL;
        foreach ($result->getPatterns() as $rule) {
            echo 'Matched rule: ' . $rule . PHP_EOL;
        }
    }
}
