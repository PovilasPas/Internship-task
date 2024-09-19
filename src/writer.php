<?php

namespace src;

class Writer
{
    public static function writeToConsole(string $string): void
    {
        echo $string . PHP_EOL;
    }
}