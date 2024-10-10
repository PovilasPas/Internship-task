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

        $insert = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ')';

        $entryWildcard = '(' . rtrim(str_repeat('?, ', count($fields)), ', ') . ')';
        $listWildcard = rtrim(str_repeat("$entryWildcard, ", $rows), ', ');

        $values = ' VALUES ' . $listWildcard;

        return $insert . $values;
    }
}
