<?php

declare(strict_types=1);

namespace App\Repository;

use App\DB\QueryBuilder;

class MatchRepository implements RepositoryInterface
{
    public function __construct(
        private readonly \PDO $connection,
        private readonly QueryBuilder $builder,
    ) {

    }

    public function insertMatches(array $matches): void
    {
        if (empty($matches)) {
            return;
        }
        $query = $this->builder->insert('matches', ['rule_id', 'word_id'], count($matches))->get();
        $data = [];
        foreach ($matches as $match) {
            $data[] = $match->getRuleId();
            $data[] = $match->getWordId();
        }
        $statement = $this->connection->prepare($query);
        $statement->execute($data);
    }
}
