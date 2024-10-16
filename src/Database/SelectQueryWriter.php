<?php

declare(strict_types=1);

namespace App\Database;

class SelectQueryWriter implements QueryWriterInterface
{
    public function write(QueryInfo $info): string
    {
        $fields = $info->getFields();
        $table = $info->getTable();

        if ($table === null || empty($fields)) {
            throw new \InvalidArgumentException('Invalid select query structure.');
        }

        $select = 'SELECT ' . implode(', ', $fields) . ' FROM ' . $table;

        $joins = $info->getJoins();

        $join = empty($joins) ? '' : ' ' . implode(' ', $joins);

        $whereConditions = $info->getWheres();

        $where = empty($whereConditions) ? '' : ' WHERE ' . implode(' ', $whereConditions);

        return $select . $join . $where;
    }
}
