<?php

declare(strict_types=1);

namespace App\Database;

enum JoinType: string
{
    case LEFT = 'LEFT JOIN';
    case RIGHT = 'RIGHT JOIN';
    case INNER = 'INNER JOIN';
}
