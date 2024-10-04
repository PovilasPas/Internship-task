<?php

declare(strict_types=1);

namespace App\Hyphenator;

class RegexHyphenator implements HyphenatorInterface
{
    private readonly array $rules;

    public function __construct(array $rules)
    {
        $preprocessedRules = [];
        foreach($rules as $rule) {
            $preprocessedRules[] = $this->preprocessWord($rule);
        }
        $this->rules = $preprocessedRules;
    }

    public function hyphenate(string $word): string
    {
        $word = $this->preprocessWord(".$word.");
        foreach ($this->rules as $rule) {
            for ($i = 0; $i < strlen($word) - strlen($rule) + 1; $i++) {
                $copy = $word;
                $wasFound = true;
                for ($j = 0; $j < strlen($rule) && $wasFound; $j++) {
                    $char1 = $copy[$i + $j];
                    $char2 = $rule[$j];
                    if (is_numeric($char1) && is_numeric($char2) && $char1 < $char2) {
                        $copy[$i + $j] = $char2;
                    } elseif (strtolower($char1) !== strtolower($char2) && (!is_numeric($char1) || !is_numeric($char2))) {
                        $wasFound = false;
                    }
                }
                if ($wasFound) {
                    $word = $copy;

                    break;
                }
            }
        }
        $word = $this->postprocessWord($word);

        return $word;
    }

    private function preprocessWord(string $word): string
    {
        $word = preg_replace('/[a-zA-Z](?!\d|\.|$)/', '${0}0', $word);

        return $word;
    }

    private function postprocessWord(string $word): string
    {
        $word = substr($word, 1, -1);
        $word = preg_replace('/[02468]/', '', $word);
        $word = preg_replace('/[13579]/', '-', $word);

        return $word;
    }
}
