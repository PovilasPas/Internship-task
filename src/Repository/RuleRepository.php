<?php

declare(strict_types=1);

namespace App\Repository;

use App\IOUtils;
use App\Database\QueryBuilder;
use App\Model\Rule;
use App\Model\Word;

class RuleRepository implements RepositoryInterface
{
    public function __construct(
        private readonly \PDO $connection,
        private readonly QueryBuilder $builder,
    ) {

    }

    public function getRulesMatchingWord(Word $word): array
    {
        $query = $this->builder
            ->select('matches', ['rules.id as id', 'rules.rule as rule'])
            ->join('rules', 'rules.id = matches.rule_id')
            ->where('matches.word_id = ?')
            ->get();
        $statement = $this->connection->prepare($query);
        $statement->execute([$word->getId()]);
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $rules = array_map(fn (array $rule): Rule => new Rule($rule['rule'], $rule['id']), $data);

        return $rules;
    }

    public function getRules(): array
    {
        $query = $this->builder->select('rules')->get();
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $rules = array_map(fn (array $rule): Rule => new Rule($rule['rule'], $rule['id']), $data);

        return $rules;
    }

    public function getRulesByPatterns(array $patterns): array
    {
        if (count($patterns) <= 0) {
            return [];
        }
        $wildcard = rtrim(str_repeat('?, ', count($patterns)), ', ');
        $query = $this->builder->select('rules')->where("rule IN ($wildcard)")->get();
        $statement = $this->connection->prepare($query);
        $statement->execute($patterns);
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $rules = array_map(fn (array $rule): Rule => new Rule($rule['rule'], $rule['id']), $data);

        return $rules;
    }

    public function loadRulesFromFile(string $filePath): void
    {
        $this->connection->beginTransaction();
        $query = $this->builder->delete('rules')->get();
        $this->connection->prepare($query)->execute();

        $rules = IOUtils::readFile($filePath);
        $query = $this->builder->insert('rules', ['rule'], count($rules), true)->get();
        $this->connection->prepare($query)->execute($rules);

        $query = $this->builder->update('words', ['hyphenated'])->get();
        $this->connection->prepare($query)->execute([null]);
        $this->connection->commit();
    }
}
