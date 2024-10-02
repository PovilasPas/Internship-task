<?php

declare(strict_types=1);

namespace App\Repository;

class AbstractRepository
{
    public function __construct(protected readonly \PDO $connection)
    {

    }
}
