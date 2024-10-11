<?php

declare(strict_types=1);

namespace App\Console\Hyphenator;


class RegexHyphenator implements HyphenatorInterface
{
    /**
     * @param string[] $rules
     */
    public function __construct(
        private readonly array $rules,
    ) {

    }

    public function hyphenate(string $word): HyphenationResult
    {
        $word = $this->preprocessString(".$word.");
        $patterns = [];
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
                    $patterns[] = preg_replace('/0/', '', $rule);
                    break;
                }
            }
        }
        $word = $this->postprocessString($word);

        return new HyphenationResult($word, $patterns);
    }

    private function preprocessString(string $word): string
    {
        $word = preg_replace('/[a-zA-Z](?!\d|\.|$)/', '${0}0', $word);

        return $word;
    }

    private function postprocessString(string $word): string
    {
        $word = substr($word, 1, -1);
        $word = preg_replace('/[02468]/', '', $word);
        $word = preg_replace('/[13579]/', '-', $word);

        return $word;
    }
}
