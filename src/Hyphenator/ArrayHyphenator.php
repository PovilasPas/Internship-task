<?php

declare(strict_types=1);

namespace App\Hyphenator;

class ArrayHyphenator implements HyphenatorInterface
{
    private readonly array $patterns;
    private readonly array $levels;

    public function __construct(array $rules)
    {
        $this->preprocessRules($rules);
    }

    private function preprocessRules(array $rules): void
    {
        $numbers = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $parsedPatterns = [];
        $parsedLevels = [];
        foreach ($rules as $rule) {
            $levels = [];
            if ($rule[0] !== '.' && !is_numeric($rule[0])) {
                $levels[] = 0;
            }
            $chars = str_split($rule);
            foreach ($chars as $idx => $char) {
                if (
                    $char !== '.'
                    && !is_numeric($char)
                    && (
                        $idx < count($chars) - 1
                        && $chars[$idx + 1] !== '.'
                        && !is_numeric($chars[$idx + 1])
                        || $idx === count($chars) - 1
                    )
                ) {
                    $levels[] = 0;
                } elseif (is_numeric($char)) {
                    $levels[] = (int)$char;
                }
            }
            $parsedPatterns[] = str_replace($numbers, '', $rule);
            $parsedLevels[] = $levels;
        }
        $this->patterns = $parsedPatterns;
        $this->levels = $parsedLevels;
    }

    public function hyphenate(string $word): string
    {
        $wordLevels = array_fill(0, strlen($word) - 1, 0);
        $wordPattern = '.' . strtolower($word) . '.';
        for ($i = 0; $i < count($this->patterns); $i++) {
            $rulePattern = $this->patterns[$i];
            $ruleLevels = $this->levels[$i];
            if (str_starts_with($rulePattern, '.') && str_starts_with($wordPattern, $rulePattern) && $this->isValidStartPattern($rulePattern, $wordPattern, $ruleLevels, $wordLevels)) {
                $to = min(count($ruleLevels), count($wordLevels));
                for ($j = 0; $j < $to; $j++) {
                    $wordLevels[$j] = max($wordLevels[$j], $ruleLevels[$j]);
                }
            } elseif (str_ends_with($rulePattern, '.') && str_ends_with($wordPattern, $rulePattern) && $this->isValidEndPattern($rulePattern, $wordPattern, $ruleLevels, $wordLevels)) {
                $start = strlen($wordPattern) - strlen($rulePattern) - 2;
                $diff = abs(min($start, 0));
                for ($j = $diff; $j < count($ruleLevels) - $diff; $j++) {
                    $pos = $start + $j;
                    $wordLevels[$pos] = max($wordLevels[$pos], $ruleLevels[$j]);
                }
            } else {
                $start = strpos($wordPattern, $rulePattern);
                while ($start !== false) {
                    if (($start !== 1 || $ruleLevels[0] === 0) && ($start !== strlen($wordPattern) - strlen($rulePattern) - 1 || $ruleLevels[count($ruleLevels) - 1] === 0)) {
                        for($j = 0; $j < count($ruleLevels); $j++) {
                            $pos = $start + $j - 2;
                            if ($pos >= 0 && $pos < count($wordLevels)) {
                                $wordLevels[$pos] = max($wordLevels[$pos], $ruleLevels[$j]);
                            }
                        }

                        break;
                    }
                    $start = strpos($wordPattern, $rulePattern, $start + 1);
                }
            }
        }
        $word = $this->formHyphenatedWord($word, $wordLevels);

        return $word;
    }

    private function formHyphenatedWord(string $word, array $levels): string
    {
        $chars = str_split($word);
        $hyphenated = [];
        foreach ($chars as $idx => $char) {
            $hyphenated[] = $char;
            if ($idx < count($levels) && ($levels[$idx] & 1) === 1) {
                $hyphenated[] = "-";
            }
        }

        return implode($hyphenated);
    }

    private function isValidStartPattern(string $rulePattern, string $wordPattern, array $ruleLevels, array $wordLevels): bool
    {
        $ruleLevelsShorter = count($ruleLevels) - 1 < count($wordLevels);
        $ruleLevelsHaveAnExtraEntry = count($ruleLevels) - 1 === count($wordLevels);
        $ruleLevelsLastEntry0 = $ruleLevels[count($ruleLevels) - 1] === 0;
        return
            $ruleLevelsShorter
            || $ruleLevelsHaveAnExtraEntry
            && $ruleLevelsLastEntry0;
    }

    private function isValidEndPattern(string $rulePattern, string $wordPattern, array $ruleLevels, array $wordLevels): bool
    {
        $ruleLevelsShorter = count($ruleLevels) - 1 < count($wordLevels);
        $ruleLevelsHaveAnExtraEntry = count($ruleLevels) - 1 === count($wordLevels);
        $ruleLevelsFirstEntry0 = $ruleLevels[0] === 0;

        return
            $ruleLevelsShorter
            || $ruleLevelsHaveAnExtraEntry
            && $ruleLevelsFirstEntry0;
    }
}