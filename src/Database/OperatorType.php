<?php

declare(strict_types=1);

namespace App\Database;

enum OperatorType: string
{
    case AND = 'AND';
    case OR = 'OR';
}
