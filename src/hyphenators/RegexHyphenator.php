<?php

namespace App\hyphenators;

use App\loggers\ILogger;

class RegexHyphenator extends AbstractHyphenator
{
    public function hyphenate(string $word): string
    {
        $word = $this->preprocessWord($word);
        foreach ($this->rules as $rule) {
            $rulePadded = preg_replace("/[a-zA-Z](?!\d|\.|$)/", '${0}0', $rule);
            for ($i = 0; $i < strlen($word) - strlen($rulePadded) + 1; $i++) {
                $copy = $word;
                $found = true;
                for ($j = 0; $j < strlen($rulePadded); $j++) {
                    $char1 = $copy[$i + $j];
                    $char2 = $rulePadded[$j];
                    if (is_numeric($char1) && is_numeric($char2) && $char1 < $char2) {
                        $copy[$i + $j] = $char2;
                    } elseif (strtolower($char1) !== strtolower($char2) && (!is_numeric($char1) || !is_numeric($char2))) {
                        $found = false;
                        break;
                    }
                }
                if ($found) {
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
        $word = "." . preg_replace("/[a-zA-Z](?!$)/", '${0}0', $word) . ".";
        return $word;
    }

    private function postprocessWord(string $word): string
    {
        $word = substr($word, 1, -1);
        $word = preg_replace("/\d*[02468]/", "", $word);
        $word = preg_replace("/\d*[13579]/", "-", $word);
        return $word;
    }
}
