<?php

declare(strict_types=1);

namespace App;

class DependencyManager
{
    private array $dependencies = [];

    public function register(string $id, callable $callable): void
    {
        $this->dependencies[$id] = $callable;
    }

    public function resolve(string $id)
    {
        if (!array_key_exists($id, $this->dependencies)) {
            throw new \InvalidArgumentException("Cannot find a way to resolve dependency '$id'");
        }
    }
}
