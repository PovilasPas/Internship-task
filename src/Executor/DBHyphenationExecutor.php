<?php

declare(strict_types=1);

namespace App\Executor;

use App\Hyphenator\ArrayHyphenator;
use App\Model\Rule;
use App\Model\Word;
use App\Processor\DBProcessor;
use App\Repository\RuleRepository;
use App\Repository\WordRepository;


class DBHyphenationExecutor implements ExecutorInterface
{
    public function __construct(private readonly \PDO $connection, private readonly string $word)
    {

    }

    public function execute(): void
    {
        $wordRepo = new WordRepository($this->connection);
        $ruleRepo = new RuleRepository($this->connection);

        $word = $wordRepo->findByWord($this->word);
        if ($word !== null && $word->getHyphenated() !== null) {
            $matchedRules = $ruleRepo->getRulesMatchingWord($word);
            echo 'Hyphenated word: ' . $word->getHyphenated() . PHP_EOL;
            foreach ($matchedRules as $rule) {
                echo 'Matched rule: ' . $rule->getRule() . PHP_EOL;
            }

            return;
        }

        if ($word === null) {
            $word = new Word($this->word);
            $wordRepo->insertWord($word);
            $word->setId((int) $wordRepo->getLastInsertedId());
        }

        $rules = array_map(function (Rule $rule) {
            return $rule->getRule();
        }, $ruleRepo->getRules());
        $hyphenator = new ArrayHyphenator($rules);

        $processor = new DBProcessor($this->connection, $hyphenator);

        $result = $processor->process([$word])[0];

        echo 'Hyphenated word: ' . $result->getWord() . PHP_EOL;
        foreach ($result->getPatterns() as $rule) {
            echo 'Matched rule: ' . $rule . PHP_EOL;
        }
    }
}
