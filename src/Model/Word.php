<?php

declare(strict_types=1);

namespace App\Model;

class Word
{
    public function __construct(private string $word, private ?int $id = null, private ?string $hyphenated = null)
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

    public function getWord(): string
    {
        return $this->word;
    }

    public function setWord(string $word): void
    {
        $this->word = $word;
    }

    public function getHyphenated(): ?string
    {
        return $this->hyphenated;
    }

    public function setHyphenated(string $hyphenated): void
    {
        $this->hyphenated = $hyphenated;
    }
}
