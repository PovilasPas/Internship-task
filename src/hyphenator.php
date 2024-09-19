<?php

namespace src;

class Hyphenator
{
    private array $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function hyphenate(string $string): string
    {
        $string = $this->preprocessString($string);
        foreach ($this->rules as $rule) {
            $rule = preg_replace("/[a-z](?!\d|\.|$)/", '${0}0', $rule);
            for ($i = 0; $i < strlen($string) - strlen($rule) + 1; $i++) {
                $copy = $string;
                $found = true;
                for ($j = 0; $j < strlen($rule); $j++) {
                    $char1 = $copy[$i + $j];
                    $char2 = $rule[$j];
                    if (is_numeric($char1) && is_numeric($char2) && $char1 < $char2) {
                        $copy[$i + $j] = $char2;
                    } else if ($char1 !== $char2 && (!is_numeric($char1) || !is_numeric($char2))) {
                        $found = false;
                        break;
                    }
                }
                if ($found) {
                    $string = $copy;
                    break;
                }
            }
        }
        return $this->postprocessString($string);
    }

    private function preprocessString(string $string): string
    {
        return "." . preg_replace("/[a-z](?!\.)/", '${0}0', $string) . ".";
    }

    private function postprocessString(string $string): string
    {
        $string = substr_replace($string, "", 0, 1);
        $string = substr_replace($string, "", -1, 1);
        $string = preg_replace("/\d*[02468]/", "", $string);
        $string = preg_replace("/\d*[13579]/", "-", $string);
        return $string;
    }
}