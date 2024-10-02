<?php

declare(strict_types=1);

namespace App\Model;

class PatternMatch
{
    public function __construct(private int $wordFk, private int $ruleFk, private ?int $id = null)
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

    public function getWordFk(): int
    {
        return $this->wordFk;
    }

    public function setWordFk(int $wordFk): void
    {
        $this->wordFk = $wordFk;
    }

    public function getRuleFk(): int
    {
        return $this->ruleFk;
    }

    public function setRuleFk(int $ruleFk): void
    {
        $this->ruleFk = $ruleFk;
    }
}
