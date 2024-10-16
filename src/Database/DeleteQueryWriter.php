<?php

declare(strict_types=1);

namespace App\Database;

class DeleteQueryWriter implements QueryWriterInterface
{
    public function write(QueryInfo $info): string
    {
        $table = $info->getTable();

        if ($table === null) {
            throw new \InvalidArgumentException('Invalid delete query structure');
        }

        $delete = 'DELETE FROM ' . $table;

        $whereConditions = $info->getWheres();

        $where = empty($whereConditions) ? '' : ' WHERE ' . implode(' ', $whereConditions);

        return $delete . $where;
    }
}
