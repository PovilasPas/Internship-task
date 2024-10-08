<?php

declare(strict_types=1);

namespace App\Hyphenator;

class HyphenationWord
{
    private array $levels;

    public function __construct(
        private readonly string $word
    ) {
        $this->levels = array_fill(0, strlen($this->word) - 1, 0);
    }

    public function getWord(): string
    {
        return $this->word;
    }

    public function getWordLength(): int
    {
        return strlen($this->word);
    }

    public function getLevels(): array
    {
        return $this->levels;
    }

    public function getLevelsLength() :int
    {
        return count($this->levels);
    }

    public function updateLevels(HyphenationRule $rule, int $indexFrom, int $indexTo, int $from): void
    {
        $ruleLevels = $rule->getLevels();
        for ($i = $indexFrom; $i < $indexTo; $i++) {
            $pos = $from + $i;
            $this->levels[$pos] = max($this->levels[$pos], $ruleLevels[$i]);
        }
    }

    public function getHyphenated(): string {
        $chars = str_split($this->word);
        $hyphenated = [];
        foreach ($chars as $index => $char) {
            $hyphenated[] = $char;
            if ($index < count($this->levels) && ($this->levels[$index] & 1) === 1) {
                $hyphenated[] = '-';
            }
        }

        return implode($hyphenated);
    }
}
