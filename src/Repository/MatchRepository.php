<?php

declare(strict_types=1);

namespace App\Repository;

class MatchRepository implements RepositoryInterface
{
    public function __construct(
        private readonly \PDO $connection,
    ) {

    }

    public function insertMatches(array $matches): void
    {
        if (empty($matches)) {
            return;
        }

        $wildcard = '(?, ?)';
        $query = 'INSERT INTO matches (rule_id, word_id) VALUES '
            . str_repeat("$wildcard, ", count($matches) - 1)
            . $wildcard;
        $data = [];
        foreach ($matches as $match) {
            $data[] = $match->getRuleId();
            $data[] = $match->getWordId();
        }
        $statement = $this->connection->prepare($query);
        $statement->execute($data);
    }
}
