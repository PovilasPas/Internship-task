<?php

declare(strict_types=1);

namespace App\Repository;

class MatchesRepository extends AbstractRepository
{
    public function insertMatches(array $matches): void
    {
        if (count($matches) <= 0) {
            return;
        }

        $wildcard = '(?, ?)';
        $query = 'INSERT INTO matches (rule_fk, word_fk) VALUES '
            . str_repeat("$wildcard, ", count($matches) - 1)
            . $wildcard;
        $data = [];
        foreach ($matches as $match) {
            $data[] = $match->getRuleFk();
            $data[] = $match->getWordFk();
        }
        $statement = $this->connection->prepare($query);
        $statement->execute($data);
    }
}
