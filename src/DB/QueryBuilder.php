<?php

declare(strict_types=1);

namespace App\DB;

class QueryBuilder
{
    private DBAction $action;

    private string $table;

    private array $fields;

    private array $conditions;

    public function __construct(
        private readonly \PDO $connection
    ) {

    }

    public function select(array $fields): QueryBuilder
    {
        $this->action = DBAction::SELECT;
        $this->fields = $fields;
        return $this;
    }

    public function from(string $table): QueryBuilder
    {
        $this->table = $table;
        return $this;
    }

    public function where(array $conditions): QueryBuilder
    {
        $this->conditions = $conditions;
        return $this;
    }
}
