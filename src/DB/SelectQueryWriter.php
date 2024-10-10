<?php

declare(strict_types=1);

namespace App\DB;

class SelectQueryWriter implements QueryWriterInterface
{
    public function writeQuery(QueryInfo $info): string
    {
        $fields = $info->getFields();
        $table = $info->getTable();

        $select = 'SELECT ' . implode(', ', $fields) . ' FROM ' . $table;

        $joins = $info->getJoins();

        $join = empty($joins) ? '' : ' ' . implode(' ', $joins);

        $whereConditions = $info->getWheres();

        $where = empty($whereConditions) ? '' : ' WHERE ' . implode(' ', $whereConditions);

        return $select . $join . $where;
    }
}
