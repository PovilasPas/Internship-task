<?php

namespace src;

use SplFileObject;

class Reader
{
    public static function readFile(string $fileName): array
    {
        $file = new SplFileObject($fileName);
        $result = [];
        while (!$file->eof()) {
            $line = trim($file->fgets());
            $result[] = $line;
        }
        $file = null;
        return $result;
    }

    public static function readFromConsole(string $prompt): string
    {
        return readline($prompt);
    }
}