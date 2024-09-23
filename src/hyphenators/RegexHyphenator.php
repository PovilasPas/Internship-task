<?php

namespace App\hyphenators;

use App\loggers\ILogger;

class RegexHyphenator extends AbstractHyphenator
{
    private ILogger $logger;

    public function __construct(array $rules, ILogger $logger)
    {
        parent::__construct($rules);
        $this->logger = $logger;
    }

    public function hyphenate(string $word): string
    {
        $word = $this->preprocessWord($word);
        $counter = 0;
        foreach ($this->rules as $rule) {
            $rulePadded = preg_replace("/[a-zA-Z](?!\d|\.|$)/", '${0}0', $rule);
            for ($i = 0; $i < strlen($word) - strlen($rulePadded) + 1; $i++) {
                $copy = $word;
                $found = true;
                for ($j = 0; $j < strlen($rulePadded); $j++) {
                    $char1 = $copy[$i + $j];
                    $char2 = $rulePadded[$j];
                    $counter++;
                    if (is_numeric($char1) && is_numeric($char2) && $char1 < $char2) {
                        $copy[$i + $j] = $char2;
                    } elseif ($char1 !== $char2 && (!is_numeric($char1) || !is_numeric($char2))) {
                        $found = false;
                        break;
                    }
                }
                if ($found) {
                    $this->logger->info("Rule matched: $rule");
                    $word = $copy;
                    break;
                }
            }
        }
        $this->logger->info("Iteration count: $counter");
        $word = $this->postprocessWord($word);
        return $word;
    }

    private function preprocessWord(string $word): string
    {
        $word = "." . preg_replace("/[a-zA-Z](?!\.)/", '${0}0', $word) . ".";
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
