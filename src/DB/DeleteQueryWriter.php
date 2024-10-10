<?php

declare(strict_types=1);

namespace App\DB;

class DeleteQueryWriter implements QueryWriterInterface
{
    public function writeQuery(QueryInfo $info): string
    {
        $table = $info->getTable();

        $delete = 'DELETE FROM ' . $table;

        $whereConditions = $info->getWheres();

        $where = empty($whereConditions) ? '' : ' WHERE ' . implode(' ', $whereConditions);

        return $delete . $where;
    }
}
