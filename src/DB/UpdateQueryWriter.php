<?php

declare(strict_types=1);

namespace App\DB;

class UpdateQueryWriter implements QueryWriterInterface
{
    public function writeQuery(QueryInfo $info): string
    {
        $table = $info->getTable();
        $fields = $info->getFields();

        if ($table === null || empty($fields)) {
            throw new \InvalidArgumentException('Invalid update query structure.');
        }

        $update = 'UPDATE ' . $table . ' SET ' . implode(' = ?,', $fields) . ' = ?';

        $wheres = $info->getWheres();

        $where = empty($wheres) ? '' : ' WHERE ' . implode(' ', $wheres);

        return $update . $where;
    }
}
