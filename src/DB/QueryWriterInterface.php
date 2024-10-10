<?php

declare(strict_types=1);

namespace App\DB;

interface QueryWriterInterface
{
    public function writeQuery(QueryInfo $info): string;
}
