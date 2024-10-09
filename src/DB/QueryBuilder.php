<?php

declare(strict_types=1);

namespace App\DB;

class QueryBuilder
{
    private QueryType $action;

    private string $table;

    private array $fields;

    private array $values;

    private array $joins = [];

    private array $whereConditions = [];

    private array $whereParameters = [];

    public function __construct(
        private readonly \PDO $connection
    ) {

    }

    public function select(array $fields = ['*']): QueryBuilder
    {
        $this->action = QueryType::SELECT;
        $this->fields = $fields;
        return $this;
    }

    public function insert(array $fields, array $values): QueryBuilder
    {
        $this->action = QueryType::INSERT;
        $this->fields = $fields;
        $this->values = $values;
        return $this;
    }

    public function update(array $fields, array $values): QueryBuilder
    {
        $this->action = QueryType::UPDATE;
        $this->fields = $fields;
        $this->values = $values;
        return $this;
    }

    public function delete(): QueryBuilder
    {
        $this->action = QueryType::DELETE;
        return $this;
    }

    public function setTable(string $table): QueryBuilder
    {
        $this->table = $table;
        return $this;
    }

    public function where(string $condition, array $parameters, ?OperatorType $operator = null): QueryBuilder
    {
        $this->whereConditions[] =  $operator ? "$operator->value $condition" : $condition;
        $this->whereParameters[] = $parameters;
        return $this;
    }

    public function join(string $table, string $condition, JoinType $type = JoinType::INNER): QueryBuilder
    {
        $this->joins[] = "$type->value $table ON $condition";
        return $this;
    }

    public function get(): array
    {
        $query = '';
        $parameters = [];
        switch ($this->action) {
            case QueryType::SELECT:
                $query .= 'SELECT ' . implode(', ', $this->fields);
                $query .= ' FROM ' . $this->table;
                if (!empty($this->joins)) {
                    $query .= ' ' . implode(' ', $this->joins);
                }
                if (!empty($this->whereConditions)) {
                    $query .= ' WHERE ' . implode(' ', $this->whereConditions);
                    $parameters = array_reduce(
                        $this->whereParameters,
                        fn (array $acc, array $parameters) => array_merge($acc, $parameters),
                        []
                    );
                }

                break;

            case QueryType::INSERT:
                $query .= 'INSERT INTO ' . $this->table;
                $query .= ' (' . implode(', ', $this->fields) . ')';
                $valuesWildcard = '(' . rtrim(str_repeat("?, ", count($this->fields)), ', ') . ')';
                $rowsWildcard = rtrim(str_repeat("$valuesWildcard, ", count($this->values)), ', ');
                $query .= ' VALUES ' . $rowsWildcard;
                $parameters = array_reduce(
                    $this->values,
                    fn (array $acc, array $parameters) => array_merge($acc, $parameters),
                    []
                );

                break;

            case QueryType::UPDATE:
                $query .= 'UPDATE ' . $this->table;
                $query .= ' SET ' . implode(' = ?, ', $this->fields) . ' = ?';
                $parameters = $this->values;
                if (!empty($this->whereConditions)) {
                    $query .= ' WHERE ' . implode(' ', $this->whereConditions);
                    $reduced = array_reduce(
                        $this->whereParameters,
                        fn (array $acc, array $parameters) => array_merge($acc, $parameters),
                        []
                    );
                    $parameters = array_merge($parameters, $reduced);
                }

                break;

            case QueryType::DELETE:
                $query .= 'DELETE FROM ' . $this->table;
                if (!empty($this->whereConditions)) {
                    $query .= ' WHERE ' . implode(' ', $this->whereConditions);
                    $parameters = array_reduce(
                        $this->whereParameters,
                        fn (array $acc, array $parameters) => array_merge($acc, $parameters),
                        []
                    );
                }

                break;
        }

        $statement = $this->connection->prepare($query);
        $statement->execute($parameters);
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $data;
    }
}
