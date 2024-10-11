<?php

declare(strict_types=1);

namespace App\DB;

class InsertQueryWriter implements QueryWriterInterface
{
    public function writeQuery(QueryInfo $info): string
    {
        $table = $info->getTable();
        $fields = $info->getFields();
        $rows = $info->getRows();

        if ($table === null || $rows === null || empty($fields)) {
            throw new \InvalidArgumentException('Invalid insert query structure');
        }

        $ignore = $info->getIgnore();

        $insert = 'INSERT ' . ($ignore ? 'IGNORE ' : '') . 'INTO ' . $table . ' (' . implode(', ', $fields) . ')';

        $entryWildcard = '(' . rtrim(str_repeat('?, ', count($fields)), ', ') . ')';
        $listWildcard = rtrim(str_repeat("$entryWildcard, ", $rows), ', ');

        $values = ' VALUES ' . $listWildcard;

        return $insert . $values;
    }
}
