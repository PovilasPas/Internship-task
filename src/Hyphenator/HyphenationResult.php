<?php

declare(strict_types=1);

namespace App\Hyphenator;

class HyphenationResult
{
    public function __construct(private string $word, private array $patterns)
    {

    }

    public function getWord(): string
    {
        return $this->word;
    }

    public function getPatterns(): array
    {
        return $this->patterns;
    }
}
