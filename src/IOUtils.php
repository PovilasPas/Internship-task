<?php

namespace App;

use SplFileObject;

class IOUtils
{
    public static function readFile(string $filePath) : array
    {
        $file = new SplFileObject($filePath);
        $lines = [];
        while (!$file->eof()) {
            $line = trim($file->fgets());
            $lines[] = $line;
        }
        $file = null;
        return $lines;
    }
}
