<?php

declare(strict_types=1);

namespace App\Console\Hyphenator;

class HyphenationRule
{
    private const array VALID_NUMBERS = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    private readonly string $pattern;
    private readonly array $levels;
    private readonly string $original;

    public function __construct(string $rule)
    {
        $this->original = $rule;
        $levels = [];
        $chars = str_split($rule);

        if (strlen($rule) > 0 && $this->isCurrentCharValid(0, $chars)) {
            $levels[] = 0;
        }

        for($i = 0; $i < count($chars); $i++) {
            if ($this->isCurrentCharValid($i, $chars) && $this->isNextCharValid($i, $chars)) {
                $levels[] = 0;
            } elseif (is_numeric($chars[$i])) {
                $levels[] = (int) $chars[$i];
            }
        }

        $this->levels = $levels;
        $this->pattern = str_replace(self::VALID_NUMBERS, '', $rule);
    }

    public function getRule(): string
    {
        return $this->pattern;
    }

    public function getRuleLength(): int
    {
        return strlen($this->pattern);
    }

    public function getLevels(): array
    {
        return $this->levels;
    }

    public function getLevelsLength() :int
    {
        return count($this->levels);
    }

    public function getOriginal(): string
    {
        return $this->original;
    }

    public function matchesStart(HyphenationWord $word): bool
    {
        $wordPattern = '.' . strtolower($word->getWord()) . '.';
        $wordLevelsLength = $word->getLevelsLength();
        $ruleLevelsLength = count($this->levels);
        $startsWithPattern = str_starts_with($wordPattern, $this->pattern);

        return $startsWithPattern
            && (
                $ruleLevelsLength < $wordLevelsLength + 1
                || $ruleLevelsLength === $wordLevelsLength + 1
                && $this->levels[$ruleLevelsLength - 1] === 0
            );
    }

    public function matchesEnd(HyphenationWord $word): bool
    {
        $wordPattern = '.' . strtolower($word->getWord()) . '.';
        $wordLevelsLength = $word->getLevelsLength();
        $ruleLevelsLength = count($this->levels);
        $endsWithPattern = str_ends_with($wordPattern, $this->pattern);

        return $endsWithPattern
            && (
                $ruleLevelsLength < $wordLevelsLength + 1
                || $ruleLevelsLength === $wordLevelsLength + 1
                && $this->levels[0] === 0
            );
    }

    public function compareMiddle(HyphenationWord $word): int
    {
        $wordPattern = strtolower($word->getWord());
        $ruleLevelsLength = $this->getLevelsLength();
        $match = strpos($wordPattern, $this->pattern);
        while ($match !== false) {
            if (
                (
                    $match !== 0
                    || $this->levels[0] === 0
                )
                && (
                    $match !== strlen($wordPattern) - strlen($this->pattern)
                    || $this->levels[$ruleLevelsLength - 1] === 0
                )
            ) {
                return $match;
            }
            $match = strpos($wordPattern, $this->pattern, $match + 1);
        }

        return -1;
    }

    private function isCurrentCharValid(int $idx, array $chars): bool
    {
        $thisChar = $chars[$idx];

        return $thisChar !== '.' && !is_numeric($thisChar);
    }

    private function isNextCharValid(int $idx, array $chars): bool
    {
        $length = count($chars);

        return $idx === $length - 1 || $idx < $length - 1 && $chars[$idx + 1] !== '.' && !is_numeric($chars[$idx + 1]);
    }
}
