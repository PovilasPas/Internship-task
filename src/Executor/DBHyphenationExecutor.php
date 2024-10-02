<?php

declare(strict_types=1);

namespace App\Executor;

use App\Hyphenator\ArrayHyphenator;
use App\Model\PatternMatch;
use App\Model\Rule;
use App\Model\Word;
use App\Repository\MatchesRepository;
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
        $matchRepo = new MatchesRepository($this->connection);

        $word = $wordRepo->findByWord($this->word);
        if ($word !== null && $word->getHyphenated() !== null) {
            $matched = $ruleRepo->getRulesMatchingWord($word);
            echo 'Hyphenated word: ' . $word->getHyphenated() . PHP_EOL;
            foreach ($matched as $rule) {
                echo 'Matched rule: ' . $rule->getRule() . PHP_EOL;
            }

            return;
        }

        if ($word === null) {
            $word = new Word($this->word);
            $wordRepo->insertWord($word);
            $word->setId((int) $wordRepo->getLastInsertedId());
        }

        $rules = array_map(function (Rule $item) {
            return $item->getRule();
        }, $ruleRepo->getRules());
        $hyphenator = new ArrayHyphenator($rules);
        $result = $hyphenator->hyphenate($this->word);
        $wordRepo->updateWord(
            $word->getId(),
            new Word($this->word, null, $result->getWord())
        );

        echo 'Hyphenated word: ' . $result->getWord() . PHP_EOL;

        $matched = $ruleRepo->getRulesByPatterns($result->getPatterns());
        $matches = array_map(function (Rule $item) use ($word) {
            return new PatternMatch($word->getId(), $item->getId());
        }, $matched);
        $matchRepo->insertMatches($matches);

        foreach ($matched as $rule) {
            echo 'Matched rule: ' . $rule->getRule() . PHP_EOL;
        }
    }
}
