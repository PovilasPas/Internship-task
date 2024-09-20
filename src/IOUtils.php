<?php

namespace src;

use SplFileObject;

class IOUtils
{
    public static function readFile(string $filePath) : array
    {
        $file = new SplFileObject($filePath);
        $lines = [];
        while(!$file->eof()) {
            $line = trim($file->fgets());
            $lines[] = $line;
        }
        $file = null;
        return $lines;
    }

    public static function readFromConsole(string $prompt) : string
    {
        return readline($prompt);
    }

    public static function writeToConsole(string $content) : void
    {
        echo $content . PHP_EOL;
    }
}