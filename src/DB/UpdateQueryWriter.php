<?php

declare(strict_types=1);

namespace App\DB;

class UpdateQueryWriter implements QueryWriterInterface
{
    public function writeQuery(QueryInfo $info): string
    {
        $table = $info->getTable();
        $fields = $info->getFields();

        $update = 'UPDATE ' . $table . ' SET ' . implode(' = ?,', $fields) . ' = ?';

        $whereConditions = $info->getWheres();

        $where = empty($whereConditions) ? '' : ' WHERE ' . implode(' ', $whereConditions);

        return $update . $where;
    }
}
