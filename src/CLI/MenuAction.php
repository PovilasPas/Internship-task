<?php

declare(strict_types=1);

namespace App\CLI;

use App\Executor\ExecutorInterface;

class MenuAction
{
    public function __construct(
        private readonly string $name,
        private readonly ExecutorInterface $action
    ) {

    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAction(): ExecutorInterface
    {
        return $this->action;
    }
}
