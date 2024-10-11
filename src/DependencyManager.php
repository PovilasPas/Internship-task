<?php

declare(strict_types=1);

namespace App;

class DependencyManager
{
    private array $dependencies = [];

    public function register(string $id, callable $resolver): void
    {
        $this->dependencies[$id] = $resolver;
    }

    public function resolve(string $id): mixed
    {
        if (!array_key_exists($id, $this->dependencies)) {
            throw new \InvalidArgumentException("Could not resolve dependency named \"$id\"");
        }

        $resolver = $this->dependencies[$id];

        return $resolver($this);
    }
}
