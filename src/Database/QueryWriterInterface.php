<?php

declare(strict_types=1);

namespace App\Database;

interface QueryWriterInterface
{
    public function write(QueryInfo $info): string;
}
