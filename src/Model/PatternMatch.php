<?php

declare(strict_types=1);

namespace App\Model;

class PatternMatch implements ModelInterface
{
    public function __construct(
        private int $wordId,
        private int $ruleId,
        private ?int $id = null,
    ) {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getWordId(): int
    {
        return $this->wordId;
    }

    public function setWordId(int $wordId): void
    {
        $this->wordId = $wordId;
    }

    public function getRuleId(): int
    {
        return $this->ruleId;
    }

    public function setRuleId(int $ruleId): void
    {
        $this->ruleId = $ruleId;
    }
}
