<?php

declare(strict_types=1);

namespace App\DB;

enum DBAction: string
{
    case SELECT = 'SELECT';
    case INSERT = 'INSERT';
    case UPDATE = 'UPDATE';
    case DELETE = 'DELETE';
}
