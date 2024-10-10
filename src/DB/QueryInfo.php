<?php

declare(strict_types=1);

namespace App\DB;

class QueryInfo
{
    private ?string $table = null;

    private ?int $rows = null;

    private array $fields = [];

    private array $joins = [];

    private array $wheres = [];

    public function getTable(): ?string
    {
        return $this->table;
    }

    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function getRows(): ?int
    {
        return $this->rows;
    }

    public function setRows(int $rows): void
    {
        $this->rows = $rows;
    }

    public function getJoins(): array
    {
        return $this->joins;
    }

    public function addJoin(string $join): void
    {
        $this->joins[] = $join;
    }

    public function getWheres(): array
    {
        return $this->wheres;
    }

    public function addWhere(string $condition): void
    {
        $this->wheres[] = $condition;
    }

    public function reset(): void
    {
        $this->table = null;
        $this->rows = null;
        $this->fields = [];
        $this->joins = [];
        $this->wheres = [];
    }

}
