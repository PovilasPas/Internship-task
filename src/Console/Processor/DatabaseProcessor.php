<?php

declare(strict_types=1);

namespace App\Console\Processor;

use App\Console\Hyphenator\HyphenatorInterface;
use App\Model\PatternMatch;
use App\Model\Rule;
use App\Model\Word;
use App\Repository\MatchRepository;
use App\Repository\RuleRepository;
use App\Repository\WordRepository;

class DatabaseProcessor
{
    public function __construct(
        private readonly WordRepository $wordRepository,
        private readonly RuleRepository $ruleRepository,
        private readonly MatchRepository $matchRepository,
        private readonly \PDO $connection,
        private readonly HyphenatorInterface $hyphenator,
    )
    {

    }

    /**
     * @param Word[] $words
     */
    public function process(array $words): array
    {
        $results = [];

        foreach ($words as $word) {
            $result = $this->hyphenator->hyphenate($word->getWord());
            $results[] = $result;

            $matches = array_map(
                fn (Rule $rule): PatternMatch => new PatternMatch($word->getId(), $rule->getId()),
                $this->ruleRepository->getRulesByPatterns($result->getPatterns())
            );

            $this->connection->beginTransaction();
            $newWord = new Word($word->getWord(), null, $result->getWord());
            $this->wordRepository->updateWord($word->getId(), $newWord);
            $this->matchRepository->insertMatches($matches);
            $this->connection->commit();
        }
        return $results;
    }
}
