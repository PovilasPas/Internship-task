<?php

declare(strict_types=1);

namespace App\Database;

class QueryBuilder
{
    private ?QueryType $type;

    private QueryInfo $info;

    public function __construct(
        private readonly QueryWriterFactory $factory,
    ) {
        $this->type = null;
        $this->info = new QueryInfo();
    }

    public function select(string $table, array $fields = ['*']): QueryBuilder
    {
        $this->type = QueryType::SELECT;

        $this->info->setTable($table);
        $this->info->setFields($fields);

        return $this;
    }

    public function insert(string $table, array $fields, int $rows = 1, bool $ignore = false): QueryBuilder
    {
        $this->type = QueryType::INSERT;

        $this->info->setTable($table);
        $this->info->setFields($fields);
        $this->info->setRows($rows);
        $this->info->setIgnore($ignore);

        return $this;
    }

    public function update(string $table, array $fields): QueryBuilder
    {
        $this->type = QueryType::UPDATE;

        $this->info->setTable($table);
        $this->info->setFields($fields);

        return $this;
    }

    public function delete(string $table): QueryBuilder
    {
        $this->type = QueryType::DELETE;

        $this->info->setTable($table);

        return $this;
    }

    public function where(string $condition, ?OperatorType $operator = null): QueryBuilder
    {
        $where = $operator ? "$operator->value $condition" : $condition;
        $this->info->addWhere($where);

        return $this;
    }

    public function join(string $table, string $condition, JoinType $type = JoinType::INNER): QueryBuilder
    {
        $join = "$type->value $table ON $condition";
        $this->info->addJoin($join);

        return $this;
    }

    public function get(): string
    {
        $writer = $this->factory->createWriter($this->type);
        $query = $writer->write($this->info);

        $this->type = null;
        $this->info->reset();

        return $query;
    }
}
