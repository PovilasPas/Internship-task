<?php

declare(strict_types=1);

namespace App\Model;

class Rule implements ModelInterface
{
    public function __construct(private string $rule, private ?int $id = null)
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getRule(): string
    {
        return $this->rule;
    }

    public function setRule(string $rule): void
    {
        $this->rule = $rule;
    }
}
