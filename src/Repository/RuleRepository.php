<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Rule;
use App\Model\Word;

class RuleRepository extends AbstractRepository
{
    public function getRulesMatchingWord(Word $word): array
    {
        $query = '
            SELECT rules.id as id, rules.rule as rule FROM matches
            INNER JOIN rules ON rules.id = matches.rule_fk
            WHERE matches.word_fk = ?
        ';
        $statement = $this->connection->prepare($query);
        $statement->execute([$word->getId()]);
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $rules = [];
        foreach ($data as $row) {
            $rules[] = new Rule($row['rule'], $row['id']);
        }
        return $rules;
    }

    public function getRules(): array
    {
        $query = 'SELECT * FROM rules';
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $rules = [];
        foreach ($data as $row) {
            $rules[] = new Rule($row['rule'], $row['id']);
        }
        return $rules;
    }

    public function getRulesByPatterns(array $patterns): array
    {
        $rules = [];
        if (count($patterns) <= 0) {
            return $rules;
        }
        $query = 'SELECT * FROM rules WHERE rule IN (' . str_repeat('?,', count($patterns) - 1) . '?)';
        $statement = $this->connection->prepare($query);
        $statement->execute($patterns);
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($data as $row) {
            $rules[] = new Rule($row['rule'], $row['id']);
        }
        return $rules;
    }
}
