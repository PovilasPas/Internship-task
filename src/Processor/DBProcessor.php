<?php

declare(strict_types=1);

namespace App\Processor;

use App\Hyphenator\HyphenatorInterface;
use App\Model\PatternMatch;
use App\Model\Rule;
use App\Model\Word;
use App\Repository\MatchesRepository;
use App\Repository\RuleRepository;
use App\Repository\WordRepository;

class DBProcessor
{
    public function __construct(
        private readonly \PDO $connection,
        private readonly HyphenatorInterface $hyphenator
    )
    {

    }

    public function process(array $words): array
    {
        $wordRepo = new WordRepository($this->connection);
        $ruleRepo = new RuleRepository($this->connection);
        $matchRepo = new MatchesRepository($this->connection);

        $results = [];

        foreach ($words as $word) {
            $result = $this->hyphenator->hyphenate($word->getWord());
            $results[] = $result;
            $matches = array_map(function (Rule $rule) use ($word) {
                return new PatternMatch($word->getId(), $rule->getId());
            }, $ruleRepo->getRulesByPatterns($result->getPatterns()));

            $this->connection->beginTransaction();
            $newWord = new Word($word->getWord(), null, $result->getWord());
            $wordRepo->updateWord(
                $word->getId(),
                $newWord
            );
            $matchRepo->insertMatches($matches);
            $this->connection->commit();
        }
        return $results;
    }
}
