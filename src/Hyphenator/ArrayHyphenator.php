<?php

declare(strict_types=1);

namespace App\Hyphenator;

readonly class ArrayHyphenator implements HyphenatorInterface
{

    private array $rules;
    private array $levels;

    public function __construct(array $rules)
    {
        $this->preprocessRules($rules);
    }

    private function preprocessRules(array $rules): void
    {
        $validNumbers = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $allRules = [];
        $allLevels = [];
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
            $allRules[] = str_replace($validNumbers, '', $rule);
            $allLevels[] = $levels;
        }
        $this->rules = $allRules;
        $this->levels = $allLevels;
    }

    public function hyphenate(string $word): string
    {
        $wordLevels = array_fill(0, strlen($word) - 1, 0);
        $modifiedWord = '.' . strtolower($word) . '.';
        for ($i = 0; $i < count($this->rules); $i++) {
            $rule = $this->rules[$i];
            $ruleLevels = $this->levels[$i];
            if (str_starts_with($rule, '.') && str_starts_with($modifiedWord, $rule) && (count($ruleLevels) - 1 < count($wordLevels) || count($ruleLevels) - 1 === count($wordLevels) && $ruleLevels[count($ruleLevels) - 1] === 0)) {
                $to = min(count($ruleLevels), count($wordLevels));
                for ($j = 0; $j < $to; $j++) {
                    $wordLevels[$j] = max($wordLevels[$j], $ruleLevels[$j]);
                }
            } elseif (str_ends_with($rule, '.') && str_ends_with($modifiedWord, $rule) && (count($ruleLevels) - 1 < count($wordLevels) || count($ruleLevels) - 1 === count($wordLevels) && $ruleLevels[0] === 0)) {
                $start = strlen($modifiedWord) - strlen($rule) - 2;
                for ($j = 0; $j < count($ruleLevels); $j++) {
                    $pos = $start + $j;
                    if($pos >= 0) {
                        $wordLevels[$pos] = max($wordLevels[$pos], $ruleLevels[$j]);
                    }
                }
            } else {
                $start = strpos($modifiedWord, $rule);
                while ($start !== false) {
                    if (!($start === 1 && $ruleLevels[0] !== 0 || $start === strlen($modifiedWord) - strlen($rule) - 1 && $ruleLevels[count($ruleLevels) - 1] !== 0)) {
                        for($j = 0; $j < count($ruleLevels); $j++) {
                            $pos = $start + $j - 2;
                            if ($pos >= 0 && $pos < count($wordLevels)) {
                                $wordLevels[$pos] = max($wordLevels[$pos], $ruleLevels[$j]);
                            }
                        }
                        break;
                    }
                    $start = strpos($modifiedWord, $rule, $start + 1);
                }
            }
        }

        $wordChars = str_split($word);
        $hyphenated = [];
        for ($i = 0; $i < count($wordChars); $i++) {
            $hyphenated[] = $wordChars[$i];
            if ($i < count($wordLevels) && ($wordLevels[$i] & 1) === 1) {
                $hyphenated[] = "-";
            }
        }

        return implode($hyphenated);
    }
}