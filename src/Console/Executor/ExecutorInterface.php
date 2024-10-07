<?php

declare(strict_types=1);

namespace App\Console\Executor;

interface ExecutorInterface
{
    public function execute(): void;
}
